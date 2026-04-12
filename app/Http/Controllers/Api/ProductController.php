<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function stockVersion()
    {
        $stockFile = storage_path('app/stock_updates.json');
        $version = file_exists($stockFile) ? filemtime($stockFile) : 0;

        return response()->json([
            'success' => true,
            'version' => $version,
        ]);
    }

    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->name ?? 'Uncategorized',
                    'image' => $product->image,
                    'is_available' => $product->is_available,
                    'is_out_of_stock' => $product->isOutOfStock(),
                    'is_low_stock' => $product->isLowStock(),
                    'is_available_for_sale' => $product->isAvailable(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? 'Uncategorized',
                'image' => $product->image,
                'image_url' => $product->image_url,
                'description' => $product->description,
                'is_available' => $product->is_available,
                'is_out_of_stock' => $product->isOutOfStock(),
                'is_low_stock' => $product->isLowStock(),
                'is_available_for_sale' => $product->isAvailable(),
            ]
        ]);
    }

    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $product->toggleAvailability();

        // Trigger real-time update to kasir
        $this->broadcastStockUpdate($product);

        return response()->json([
            'success' => true,
            'message' => 'Product availability updated successfully',
            'data' => [
                'id' => $product->id,
                'is_available' => $product->is_available,
                'stock' => $product->stock,
                'is_available_for_sale' => $product->isAvailable(),
            ]
        ]);
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $product = Product::findOrFail($id);
        $product->stock = $request->stock;
        
        // Auto disable if stock is 0
        if ($product->stock <= 0) {
            $product->is_available = false;
        } else {
            // Auto enable if stock > 0 and was disabled
            if (!$product->is_available) {
                $product->is_available = true;
            }
        }
        
        $product->save();

        // Trigger real-time update to kasir
        $this->broadcastStockUpdate($product);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => [
                'id' => $product->id,
                'stock' => $product->stock,
                'is_available' => $product->is_available,
                'is_available_for_sale' => $product->isAvailable(),
            ]
        ]);
    }

    private function broadcastStockUpdate($product)
    {
        // Create push notification for real-time update
        $updateData = [
            'type' => 'stock_update',
            'product_id' => $product->id,
            'stock' => $product->stock,
            'is_available' => $product->is_available,
            'timestamp' => now()->toISOString(),
            'action' => 'admin_update'
        ];

        // Write once with lock to avoid duplicate/noisy file modification triggers.
        file_put_contents(
            storage_path('app/stock_updates.json'),
            json_encode($updateData),
            LOCK_EX
        );
        
        // Skip per-request logging here to keep admin update response snappy.
    }

    public function getStockStats()
    {
        $totalProducts = Product::count();
        $availableProducts = Product::available()->count();
        $outOfStockProducts = Product::outOfStock()->count();
        $lowStockProducts = Product::lowStock()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $totalProducts,
                'available_products' => $availableProducts,
                'out_of_stock_products' => $outOfStockProducts,
                'low_stock_products' => $lowStockProducts,
            ]
        ]);
    }
}
