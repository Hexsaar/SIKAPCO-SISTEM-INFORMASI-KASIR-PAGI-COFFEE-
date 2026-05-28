<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Product;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::with('recipes.product')->get();
        return view('ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('ingredients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        Ingredient::create($validated);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $ingredient->update($validated);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil diperbarui');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Bahan baku berhasil dihapus');
    }

    public function stockAdjust(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric',
            'type' => 'required|in:add,subtract',
        ]);

        if ($validated['type'] === 'add') {
            $ingredient->updateStock($validated['quantity']);
            $message = "Stok {$ingredient->name} berhasil ditambah {$validated['quantity']} {$ingredient->unit}";
        } else {
            $ingredient->updateStock(-$validated['quantity']);
            $message = "Stok {$ingredient->name} berhasil dikurangi {$validated['quantity']} {$ingredient->unit}";
        }

        return redirect()->route('admin.ingredients.index')
            ->with('success', $message);
    }

    // Recipe management
    public function recipeIndex()
    {
        $recipes = Recipe::with(['product', 'ingredient'])->get();
        $products = Product::all();
        $ingredients = Ingredient::active()->get();
        
        return view('ingredients.recipes', compact('recipes', 'products', 'ingredients'));
    }

    public function recipeStore(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0',
        ]);

        // Check if recipe already exists
        $existing = Recipe::where('product_id', $validated['product_id'])
                          ->where('ingredient_id', $validated['ingredient_id'])
                          ->first();

        if ($existing) {
            return redirect()->route('admin.ingredients.recipes')
                ->with('error', 'Resep untuk menu dan bahan ini sudah ada');
        }

        Recipe::create($validated);

        return redirect()->route('admin.ingredients.recipes')
            ->with('success', 'Resep berhasil ditambahkan');
    }

    public function recipeDestroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('admin.ingredients.recipes')
            ->with('success', 'Resep berhasil dihapus');
    }

    // Check stock availability for a product
    public function checkProductStock(Product $product)
    {
        $recipes = Recipe::where('product_id', $product->id)->get();
        $canMake = true;
        $limitingIngredients = [];

        foreach ($recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            $requiredQuantity = $recipe->quantity;
            
            if ($ingredient->stock < $requiredQuantity) {
                $canMake = false;
                $limitingIngredients[] = [
                    'name' => $ingredient->name,
                    'available' => $ingredient->stock,
                    'required' => $requiredQuantity,
                    'unit' => $ingredient->unit,
                ];
            }
        }

        return response()->json([
            'can_make' => $canMake,
            'limiting_ingredients' => $limitingIngredients,
        ]);
    }

    // Calculate how many products can be made from current stock
    public function calculateMaxProducts(Product $product)
    {
        $recipes = Recipe::where('product_id', $product->id)->get();
        
        if ($recipes->isEmpty()) {
            return response()->json([
                'max_products' => null,
                'message' => 'Tidak ada resep untuk menu ini',
            ]);
        }

        $maxProducts = null;

        foreach ($recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            $requiredQuantity = $recipe->quantity;
            
            if ($requiredQuantity > 0) {
                $canMake = floor($ingredient->stock / $requiredQuantity);
                
                if ($maxProducts === null || $canMake < $maxProducts) {
                    $maxProducts = $canMake;
                }
            }
        }

        return response()->json([
            'max_products' => $maxProducts,
        ]);
    }
}
