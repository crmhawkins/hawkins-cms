<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeConnectGateway implements PaymentGateway
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient((string) config('services.stripe.secret'));
    }

    public function createCheckoutSession(Order $order, array $options = []): CheckoutSession
    {
        $tenant = Tenant::find($order->tenant_id);

        $params = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $this->buildLineItems($order),
            'success_url' => $options['success_url'] ?? route('shop.success', ['order' => $order->order_number]),
            'cancel_url' => $options['cancel_url'] ?? route('shop.cancel'),
            'metadata' => [
                'order_number' => $order->order_number,
                'tenant_id' => $order->tenant_id,
            ],
            'customer_email' => $order->customer_email,
        ];

        if ($tenant && $tenant->stripe_account_id) {
            $params['payment_intent_data'] = [
                'application_fee_amount' => (int) ($order->total * 0.02),
                'transfer_data' => ['destination' => $tenant->stripe_account_id],
            ];
        }

        $session = $this->stripe->checkout->sessions->create($params);

        return new CheckoutSession(
            id: $session->id,
            url: $session->url,
            provider: 'stripe_connect',
        );
    }

    public function handleWebhook(Request $request): WebhookResult
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                (string) config('services.stripe.webhook_secret')
            );
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new WebhookResult(handled: false, event: 'invalid_signature');
        } catch (\UnexpectedValueException $e) {
            return new WebhookResult(handled: false, event: 'invalid_payload');
        }

        $orderId = null;

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderNumber = $session->metadata->order_number ?? null;

            if ($orderNumber) {
                $order = Order::withoutGlobalScopes()->where('order_number', $orderNumber)->first();
                if ($order) {
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'payment_id' => $session->id,
                        'stripe_payment_intent_id' => $session->payment_intent ?? null,
                    ]);
                    $orderId = (string) $order->id;
                }
            }
        }

        // Idempotency: store event
        \App\Models\WebhookEvent::updateOrCreate(
            ['event_id' => $event->id],
            [
                'gateway' => 'stripe',
                'type' => $event->type,
                'payload' => json_decode(json_encode($event), true),
                'processed_at' => now(),
            ]
        );

        return new WebhookResult(
            handled: true,
            event: $event->type,
            orderId: $orderId,
        );
    }

    public function getThreeDsRedirectUrl(Order $order): ?string
    {
        return null; // Stripe handles 3DS natively in checkout
    }

    public function refund(Order $order, int $amountCents): RefundResult
    {
        $params = ['payment_intent' => $order->stripe_payment_intent_id];
        if ($amountCents > 0) {
            $params['amount'] = $amountCents;
        }

        $refund = $this->stripe->refunds->create($params);

        $order->update([
            'status' => 'refunded',
            'refund_id' => $refund->id,
        ]);

        return new RefundResult(
            success: $refund->status === 'succeeded' || $refund->status === 'pending',
            refundId: $refund->id,
            amountCents: (int) $refund->amount,
        );
    }

    public function onboardTenant(Tenant $tenant): OnboardingLink
    {
        $account = $this->stripe->accounts->create([
            'type' => 'express',
            'country' => 'ES',
        ]);

        $tenant->update(['stripe_account_id' => $account->id]);

        $link = $this->stripe->accountLinks->create([
            'account' => $account->id,
            'refresh_url' => url('/hawkins/stripe/onboard/refresh/' . $tenant->id),
            'return_url' => url('/hawkins/stripe/onboard/return/' . $tenant->id),
            'type' => 'account_onboarding',
        ]);

        return new OnboardingLink(
            url: $link->url,
            provider: 'stripe_connect',
        );
    }

    public function offboardTenant(Tenant $tenant): void
    {
        $tenant->update(['stripe_account_id' => null]);
    }

    private function buildLineItems(Order $order): array
    {
        return collect($order->items)->map(fn ($item) => [
            'price_data' => [
                'currency' => strtolower($order->currency ?? 'eur'),
                'product_data' => ['name' => $item['name'] ?? 'Producto'],
                'unit_amount' => (int) ($item['price'] ?? $item['price_at_add'] ?? 0),
            ],
            'quantity' => (int) ($item['qty'] ?? 1),
        ])->values()->all();
    }
}
