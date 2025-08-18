<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class ProductController extends Controller
{
    public function assignToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'image' => 'required|image|max:2048',
            'price' => 'required|numeric|min:0',
            'size' => 'required|string|max:255',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        // If your Product model has a user_id field:
        $product = Product::create([
            'user_id' => $request->user_id,
            'image' => $imagePath,
            'price' => $request->price,
            'size' => $request->size,
        ]);

        return redirect()->back()->with('success', 'Product added to user successfully!');
    }

    public function userProducts()
    {
        $products = Product::where('user_id', Auth::id())->get();
        return view('user.products', compact('products'));
    }

    public function destroy(Product $product)
    {
        // Optionally check if the user owns the product
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted.');
    }

    public function pay(Product $product)
    {
        // Your payment logic here
        // For now, just redirect back
        return redirect()->back()->with('success', 'Proceed to payment for product ID: ' . $product->id);
    }
}
