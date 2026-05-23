<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\BarcodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(private BarcodeService $barcodeService) {}

    public function index(Request $request)
    {
        $query = Product::with('category')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            }))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->stock_filter === 'low', fn($q) => $q->whereColumn('stock_total', '<=', 'min_stock'))
            ->when($request->stock_filter === 'empty', fn($q) => $q->where('stock_total', 0))
            ->orderBy('name');

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // Generate SKU and barcode
        $data['sku']     = Product::generateSku($data['category_id']);
        $data['barcode'] = $this->barcodeService->buildBarcodeValue($data['sku']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', "Produk {$product->name} berhasil ditambahkan.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'batches' => fn($q) => $q->orderBy('received_date')]);
        $barcodeBase64 = $this->barcodeService->generateBarcode($product->barcode);

        return view('products.show', compact('product', 'barcodeBase64'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        if ($product->stock_total > 0) {
            return back()->with('error', 'Produk masih memiliki stok dan tidak dapat dihapus.');
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function printLabel(Product $product)
    {
        $barcodeBase64 = $this->barcodeService->generateBarcode($product->barcode);
        return view('products.label', compact('product', 'barcodeBase64'));
    }

    /** API endpoint: look up product by barcode for scan workflow */
    public function findByBarcode(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)
            ->orWhere('sku', $request->barcode)
            ->with('category')
            ->first();

        if (! $product) {
            return response()->json(['error' => 'Produk tidak ditemukan.'], 404);
        }

        return response()->json([
            'id'          => $product->id,
            'name'        => $product->name,
            'sku'         => $product->sku,
            'barcode'     => $product->barcode,
            'sell_price'  => $product->sell_price,
            'stock_total' => $product->stock_total,
            'unit'        => $product->unit,
            'image_url'   => $product->image_url,
        ]);
    }
}
