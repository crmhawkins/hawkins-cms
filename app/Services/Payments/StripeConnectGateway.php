<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeConnectGateway implements PaymentGateway
{
    private StripeClient $stripe;

    public function __construct()
    {
        $secret = SiteSettings::instance()->stripe_secret_key ?: (string) config('services.stripe.secret');
        $this->stripe = new StripeClient($secret);
    }

    public function createCheckoutSession(Order $order, array $options = []): CheckoutSession
    {
        $params = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $this->buildLineItems($order),
            'success_url' => $options['success_url'] ?? route('shop.success', ['order' => $order->order_number]),
            'cancel_url' => $options['cancel_url'] ?? route('shop.cancel'),
            'metadata' => [
                'order_number' => $order->order_number,
                'cart_id'      => (string) ($options['cart_id'] ?? ''),
            ],
            'customer_email' => $order->customer_email,
        ];

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

        $secret = SiteSettings::instance()->stripe_webhook_secret ?: (string) config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $secret);
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
                    \App\Models\AuditLog::record('order.paid', ['order_id' => $order->id, 'payment_id' => $session->id]);
                    $orderId = (string) $order->id;

                    // Clear the cart now that payment is confirmed
                    $cartId = $session->metadata->cart_id ?? null;
                    if ($cartId) {
                        \App\Models\Cart::find((int) $cartId)?->update(['items' => []]);
                    }
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
        \App\Models\AuditLog::record('order.refunded', ['order_id' => $order->id, 'refund_id' => $refund->id, 'amount' => $refund->amount]);

        return new RefundResult(
            success: $refund->status === 'succeeded' || $refund->status === 'pending',
            refundId: $refund->id,
            amountCents: (int) $refund->amount,
        );
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
