<?php

namespace App\Http\Controllers\API;

use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
