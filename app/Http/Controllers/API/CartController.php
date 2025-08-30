<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        if ($product->stock < $request->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock. Only ' . $product->stock . ' items available.'
            ]);
        }

        $cart = Cart::getCart($user->id);

        // Find existing cart item
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Increment quantity
            $newQuantity = $cartItem->quantity + $request->quantity;

            // Check stock availability for new quantity
            if ($product->stock < $newQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Insufficient stock. Only ' . $product->stock . ' items available.'
                ]);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->price = $product->price * $newQuantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price * $request->quantity
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart',
            'cart_item' => $cartItem->load('product')
        ], 201);
    }
}
