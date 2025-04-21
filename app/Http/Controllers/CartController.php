<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request, $productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $user = Auth::user(); // Get the logged-in user (or null if guest)

        $cart = Cart::where('user_id', $user ? $user->id : null)
                    ->where('session_id', $user ? null : session()->getId())
                    ->where('product_id', $product->id)
                    ->first();

        if ($cart) {
            // If the product already exists in the cart, increase the quantity
            $cart->quantity += $request->quantity;
            $cart->save();
        } else {
            // Add new product to the cart
            Cart::create([
                'user_id' => $user ? $user->id : null,
                'session_id' => $user ? null : session()->getId(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    public function index()
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user ? $user->id : null)
                         ->where('session_id', $user ? null : session()->getId())
                         ->get();

        return view('cart.index', compact('cartItems'));
    }

    public function removeFromCart($cartId)
    {
        $cartItem = Cart::find($cartId);

        if ($cartItem) {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
        }

        return redirect()->route('cart.index')->with('error', 'Item not found.');
    }
}
