<?php

namespace App\Services;

use App\Models\Order;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    public function process(Order $order, $paymentMethodId)
    {
        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int)($order->total * 100),
                'currency' => 'usd',
                'payment_method' => $paymentMethodId,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'description' => 'Order Payment - ' . $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id
                ],
                'return_url' => config('app.url') . '/payment/complete',
            ]);
            return ['status' => $paymentIntent->status];
        } catch (ApiErrorException $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }
}
