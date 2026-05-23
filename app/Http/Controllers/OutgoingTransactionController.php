<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutgoingTransactionRequest;
use App\Models\OutgoingTransaction;
use App\Models\Product;
use App\Services\FifoService;
use Illuminate\Http\Request;

class OutgoingTransactionController extends Controller
{
    public function __construct(private FifoService $fifoService) {}

    public function index(Request $request)
    {
        $transactions = OutgoingTransaction::with(['product', 'user', 'fifoDetails.batch'])
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('transaction_code', 'like', "%{$request->search}%")
                  ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            }))
            ->when($request->date_from, fn($q) => $q->where('transaction_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->where('transaction_date', '<=', $request->date_to))
            ->latest('transaction_date')
            ->paginate(15)
            ->withQueryString();

        return view('outgoing.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_total', '>', 0)
            ->orderBy('name')
            ->get();

        return view('outgoing.create', compact('products'));
    }

    public function store(OutgoingTransactionRequest $request)
    {
        $data    = $request->validated();
        $product = Product::findOrFail($data['product_id']);

        try {
            $outgoing = $this->fifoService->processOutgoing(
                product:          $product,
                quantity:         $data['quantity'],
                sellPrice:        $data['sell_price'],
                userId:           auth()->id(),
                transactionDate:  $data['transaction_date'],
                referenceNumber:  $data['reference_number'] ?? null,
                notes:            $data['notes'] ?? null,
            );

            return redirect()
                ->route('outgoing.show', $outgoing)
                ->with('success', "Barang keluar berhasil dicatat. HPP: Rp " . number_format($outgoing->total_hpp, 0, ',', '.'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(OutgoingTransaction $outgoing)
    {
        $outgoing->load(['product', 'user', 'fifoDetails.batch']);
        return view('outgoing.show', compact('outgoing'));
    }
}
