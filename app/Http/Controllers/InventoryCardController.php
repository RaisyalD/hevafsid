<?php

namespace App\Http\Controllers;

use App\Models\InventoryCard;
use App\Models\Product;
use App\Services\FifoService;
use Illuminate\Http\Request;

class InventoryCardController extends Controller
{
    public function __construct(private FifoService $fifoService) {}

    public function index(Request $request)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        if ($request->product_id) {
            return redirect()->route('inventory-card.show', $request->product_id);
        }

        return view('inventory-card.index', compact('products'));
    }

    public function show(Request $request, Product $product)
    {
        $query = InventoryCard::where('product_id', $product->id)
            ->with('batch')
            ->when($request->date_from, fn($q) => $q->where('transaction_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->where('transaction_date', '<=', $request->date_to))
            ->orderBy('transaction_date')
            ->orderBy('id');

        $cards     = $query->paginate(30)->withQueryString();
        $valuation = $this->fifoService->getStockValuation($product);
        $products  = Product::where('is_active', true)->orderBy('name')->get();

        return view('inventory-card.show', compact('product', 'cards', 'valuation', 'products'));
    }
}
