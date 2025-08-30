<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Order;
use App\Models\CartItem;
use App\Services\PaymentService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CheckoutRequest;

class CheckoutController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();

        $subtotal = $cartItems->sum('price');
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

    public function process(CheckoutRequest $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $subtotal = $cartItems->sum('price');
        $tax = $subtotal * 0.1;
        $shipping = 5.00;
        $total = $subtotal + $tax + $shipping;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user->id,
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

            $paymentResult = $this->paymentService->process($order, $request->payment_method_id);

            if ($paymentResult['status'] === 'succeeded') {
                $order->update(['status' => 'completed']);
                foreach ($cartItems as $cartItem) {
                    $order->orderItems()->create([
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                    ]);
                }
                CartItem::where('cart_id', $cart->id)->delete();
                $cart->delete();
                DB::commit();
                return response()->json([
                    'message' => 'Order placed successfully',
                    'order_number' => $order->order_number
                ]);
            } else {
                $order->update(['status' => 'failed']);
                DB::rollBack();
                return response()->json([
                    'message' => 'Payment failed',
                    'error' => $paymentResult['error'] ?? null
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Checkout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
