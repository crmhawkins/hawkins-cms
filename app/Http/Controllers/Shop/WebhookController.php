<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use App\Services\Payments\StripeConnectGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookController extends Controller
{
    public function stripe(Request $request)
    {
        $eventId = null;
        try {
            $payload = json_decode($request->getContent(), true);
            $eventId = $payload['id'] ?? null;
        } catch (\Throwable $e) {
            // ignored
        }

        if ($eventId) {
            $existing = WebhookEvent::where('event_id', $eventId)->first();
            if ($existing && $existing->processed_at) {
                return response('OK', Response::HTTP_OK);
            }
        }

        $gateway = new StripeConnectGateway();
        $result = $gateway->handleWebhook($request);

        if (! $result->handled) {
            return response($result->event, Response::HTTP_BAD_REQUEST);
        }

        return response('OK', Response::HTTP_OK);
    }
}
