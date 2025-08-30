<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        $subtotal = $cartItems->sum('total');

        return response()->json([
            'cart_items' => $cartItems,
            'summary' => [
                'subtotal' => $subtotal,
                'item_count' => $cartItems->sum('quantity'),
                'unique_items' => $cartItems->count()
            ]
        ]);
    }

    
}
