<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Order;
use App\Models\CartItem;
use Stripe\StripeClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $cartItems = CartItem::with('product')
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price;
        });

        $tax = $subtotal * 0.1; // 10% tax
        $shipping = 5.00; // Fixed shipping cost
        $total = $subtotal + $tax + $shipping;

        return response()->json([
            'cart_items' => $cartItems,
            'summary' => [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total
            ]
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'payment_method' => 'required|string|in:stripe,paypal'
        ]);

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price ;
        });
       
        $tax = $subtotal * 0.1;
        $shipping = 5.00;
        $total = $subtotal + $tax + $shipping;

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'cart_id' => $cartItems->first()->id,
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'shipping_address' => $request->shipping_address,
            'billing_address' => $request->billing_address ?? $request->shipping_address,
            'customer_email' => $user->email,
            'customer_phone' => $request->customer_phone,
            'status' => 'pending'
        ]);

        // Process payment (simulated)
        $paymentSuccess = $this->processPayment($order, $request->payment_method_id);
        // dd($paymentSuccess);
        if ($paymentSuccess) {
            $order->update(['status' => 'processing']);

            // Clear cart
            // Store cart items as order items
            foreach ($cartItems as $cartItem) {
                $order->orderItems()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            // Delete cart and cart items
            // CartItem::where('cart_id', $cart->id)->delete();
            // $cart->delete();

            return response()->json([
                'message' => 'Order placed successfully',
                'order_number' => $order->order_number
            ]);
        } else {
            $order->update(['status' => 'failed']);

            return response()->json([
                'message' => 'Payment failed'
            ], 400);
        }
    }

    private function processPayment(Order $order, $paymentMethodId)
    {
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int)($order->total * 100), // Convert to cents
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
                'return_url' => config('app.url') . '/payment/complete', // For redirect-based flows
            ]);

            return $paymentIntent;

        } catch (ApiErrorException $e) {
            // Handle error
            return false;
        }
    }
}
