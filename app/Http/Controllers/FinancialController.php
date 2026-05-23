<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{ 
    public function index(Request $request)
    {
        $query = FinancialTransaction::with('user')
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->date_from, fn($q) => $q->where('transaction_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->where('transaction_date', '<=', $request->date_to))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                  ->orWhere('transaction_code', 'like', "%{$request->search}%");
            }))
            ->latest('transaction_date');

        $transactions = $query->paginate(20)->withQueryString();

        // Summary for filtered period
        $summary = FinancialTransaction::when($request->date_from, fn($q) => $q->where('transaction_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('transaction_date', '<=', $request->date_to))
            ->selectRaw("
                SUM(CASE WHEN type='cash_in' THEN amount ELSE 0 END)  AS total_in,
                SUM(CASE WHEN type='cash_out' THEN amount ELSE 0 END) AS total_out,
                SUM(CASE WHEN type='cash_in' THEN amount ELSE -amount END) AS net
            ")
            ->first();

        return view('financial.index', compact('transactions', 'summary'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => 'required|in:cash_in,cash_out',
            'category'         => 'required|in:sales,purchase,reject_loss,operational,other',
            'amount'           => 'required|numeric|min:0',
            'description'      => 'required|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $data['transaction_code'] = FinancialTransaction::generateCode();
        $data['user_id']          = auth()->id();

        FinancialTransaction::create($data);

        return back()->with('success', 'Transaksi keuangan berhasil dicatat.');
    }

    public function destroy(FinancialTransaction $financial)
    {
        // Only allow deleting manually-created records (not auto-generated ones)
        if (in_array($financial->category, ['sales', 'purchase', 'reject_loss'])) {
            return back()->with('error', 'Transaksi otomatis tidak dapat dihapus.');
        }

        $financial->delete();
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function profitLoss(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $data = FinancialTransaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $mon)
            ->selectRaw("
                category,
                type,
                SUM(amount) AS total
            ")
            ->groupBy('category', 'type')
            ->get();

        $revenue   = $data->where('category', 'sales')->where('type', 'cash_in')->sum('total');
        $hpp       = $data->where('category', 'purchase')->where('type', 'cash_out')->sum('total');
        $grossProfit = $revenue - $hpp;

        $rejectLoss = $data->where('category', 'reject_loss')->sum('total');
        $operational = $data->where('category', 'operational')->sum('total');
        $netProfit   = $grossProfit - $rejectLoss - $operational;

        return view('financial.profit-loss', compact(
            'month', 'revenue', 'hpp', 'grossProfit',
            'rejectLoss', 'operational', 'netProfit'
        ));
    }
}
