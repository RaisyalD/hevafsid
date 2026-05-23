<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectItemRequest;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\RejectItem;
use App\Services\FifoService;
use Illuminate\Http\Request;

class RejectItemController extends Controller
{
    public function __construct(private FifoService $fifoService) {}

    public function index(Request $request)
    {
        $rejects = RejectItem::with(['product', 'batch', 'user'])
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('reject_code', 'like', "%{$request->search}%")
                  ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            }))
            ->when($request->reject_type, fn($q) => $q->where('reject_type', $request->reject_type))
            ->when($request->date_from, fn($q) => $q->where('reject_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->where('reject_date', '<=', $request->date_to))
            ->latest('reject_date')
            ->paginate(15)
            ->withQueryString();

        $totalLoss = RejectItem::whereMonth('reject_date', now()->month)->sum('total_loss');

        return view('reject.index', compact('rejects', 'totalLoss'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_total', '>', 0)
            ->orderBy('name')
            ->get();

        return view('reject.create', compact('products'));
    }

    public function store(RejectItemRequest $request)
    {
        $data    = $request->validated();
        $product = Product::findOrFail($data['product_id']);
        $batch   = $data['product_batch_id']
            ? ProductBatch::find($data['product_batch_id'])
            : null;

        if ($product->stock_total < $data['quantity']) {
            return back()->withInput()->with('error', 'Stok tidak mencukupi untuk di-reject.');
        }

        try {
            $reject = $this->fifoService->processReject(
                product:    $product,
                quantity:   $data['quantity'],
                batch:      $batch,
                rejectType: $data['reject_type'],
                reason:     $data['reason'],
                userId:     auth()->id(),
                rejectDate: $data['reject_date'],
            );

            return redirect()
                ->route('reject.index')
                ->with('success', "Barang reject berhasil dicatat. Kerugian: Rp " . number_format($reject->total_loss, 0, ',', '.'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
