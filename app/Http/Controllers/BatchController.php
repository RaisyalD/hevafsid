<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\FifoService;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function __construct(private FifoService $fifoService) {}

    public function index(Request $request)
    {
        $batches = ProductBatch::with('product.category')
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('batch_code', 'like', "%{$request->search}%")
                  ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            }))
            ->orderByDesc('received_date')
            ->paginate(20)
            ->withQueryString();

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('batches.index', compact('batches', 'products'));
    }

    public function show(ProductBatch $batch)
    {
        $batch->load([
            'product.category',
            'incomingTransaction.supplier',
            'fifoDetails.outgoingTransaction',
        ]);

        $valuation = $this->fifoService->getStockValuation($batch->product);

        return view('batches.show', compact('batch', 'valuation'));
    }

    /** API: get active batches for a product (for scan/select UI) */
    public function forProduct(Product $product)
    {
        $batches = $product->activeBatches()
            ->select('id', 'batch_code', 'qty_remaining', 'cost_price', 'received_date')
            ->get();

        return response()->json($batches);
    }
}
