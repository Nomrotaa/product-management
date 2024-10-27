<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{

public function index(Request $request)
{
    if ($request->ajax()) {
        $products = Product::select(['id', 'title', 'description', 'price']);
        return DataTables::of($products)
            ->addColumn('action', function ($product) {
                return '
                    <a href="javascript:void(0)" data-id="' . $product->id . '" class="btn btn-sm btn-primary edit-btn">Edit</a>
                    <a href="javascript:void(0)" data-id="' . $product->id . '" class="btn btn-sm btn-danger delete-btn">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('products.index');
}


    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id); // Find the product by ID
        return response()->json($product); // Return product data as JSON
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        // Update the product
        $product->update($request->only(['title', 'description', 'price']));

        return response()->json(['success' => 'Product updated successfully']);
    }

    // ProductController.php

public function destroy($id)
{
    try {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => 'Product deleted successfully!']);
    } catch (\Exception $e) {
        \Log::error($e->getMessage()); // Log the error for debugging
        return response()->json(['error' => 'Unable to delete product.'], 500);
    }
}

}

