<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomingTransactionRequest;
use App\Models\IncomingTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\FifoService;
use Illuminate\Http\Request;

class IncomingTransactionController extends Controller
{
    public function __construct(private FifoService $fifoService) {}

    public function index(Request $request)
    {
        $transactions = IncomingTransaction::with(['product', 'supplier', 'user', 'batch'])
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('transaction_code', 'like', "%{$request->search}%")
                  ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            }))
            ->when($request->date_from, fn($q) => $q->where('received_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->where('received_date', '<=', $request->date_to))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest('received_date')
            ->paginate(15)
            ->withQueryString();

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('incoming.index', compact('transactions', 'suppliers'));
    }

    public function create()
    {
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('incoming.create', compact('products', 'suppliers'));
    }

    public function store(IncomingTransactionRequest $request)
    {
        $data    = $request->validated();
        $product = Product::findOrFail($data['product_id']);

        try {
            $result = $this->fifoService->processIncoming(
                product:        $product,
                quantity:       $data['quantity'],
                costPrice:      $data['cost_price'],
                supplierId:     $data['supplier_id'] ?? 0,
                userId:         auth()->id(),
                receivedDate:   $data['received_date'],
                invoiceNumber:  $data['invoice_number'] ?? null,
                notes:          $data['notes'] ?? null,
            );

            return redirect()
                ->route('incoming.show', $result['incoming']->id)
                ->with('success', "Barang masuk berhasil dicatat. Batch: {$result['batch']->batch_code}");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(IncomingTransaction $incoming)
    {
        $incoming->load(['product', 'supplier', 'user', 'batch']);
        return view('incoming.show', compact('incoming'));
    }
}
