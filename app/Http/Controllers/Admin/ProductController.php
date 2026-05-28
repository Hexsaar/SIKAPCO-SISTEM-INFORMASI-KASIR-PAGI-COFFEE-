<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        // Define category order: Coffee → Non Coffee → Coffee Milk → Snack → Bottle → (lainnya)
        $products = Product::with('category')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->orderByRaw("CASE categories.name 
                WHEN 'Coffee' THEN 1
                WHEN 'Non Coffee' THEN 2
                WHEN 'Coffee Milk' THEN 3
                WHEN 'Snack' THEN 4
                WHEN 'Bottle' THEN 5
                ELSE 6
            END")
            ->orderBy('categories.name')
            ->orderBy('products.name')
            ->select('products.*')
            ->paginate(10);
            
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_best_seller' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_best_seller'] = $request->has('is_best_seller');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'products/' . $imageName;
        }

        $product = Product::create($data);

        // Return JSON untuk AJAX request
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'product' => $product
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'is_best_seller' => 'boolean'
    ]);

    $data = $request->all();
    $data['slug'] = Str::slug($request->name);
    $data['is_best_seller'] = $request->has('is_best_seller');

    // Handle image upload
    if ($request->hasFile('image')) {
        // Delete old image
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/products'), $imageName);
        $data['image'] = 'products/' . $imageName;
    }

    $product->update($data);

    // Return JSON untuk AJAX request
    if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => $product
        ]);
    }

    return redirect()->route('admin.products.index')
        ->with('success', 'Product updated successfully.');
}

    public function destroy(Product $product)
{
    // Hapus semua order_items yang terkait dengan produk ini
    $product->orderItems()->delete();
    
    // Hapus file gambar
    if ($product->image && file_exists(public_path($product->image))) {
        unlink(public_path($product->image));
    }

    // Hapus produk
    $product->delete();

    return redirect()->route('admin.products.index')
        ->with('success', 'Product deleted successfully.');
}
}