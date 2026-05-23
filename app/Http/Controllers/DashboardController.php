<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\OutgoingTransaction;
use App\Models\IncomingTransaction;
use App\Models\Product;
use App\Models\RejectItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);

        // Clamp to valid range
        $year  = max(2020, min($year,  now()->year));
        $month = max(1,    min($month, 12));

        // Prevent selecting future months
        if ($year === now()->year && $month > now()->month) {
            $month = now()->month;
        }

        $isCurrentMonth = ($year === now()->year && $month === now()->month);
        $today          = today()->toDateString();
        $selectedPeriod = Carbon::createFromDate($year, $month, 1);

        // ── KPI stats ────────────────────────────────────────────────────
        $stats = [
            'total_products'     => Product::where('is_active', true)->count(),
            'total_stock'        => Product::sum('stock_total'),
            'incoming_today'     => IncomingTransaction::where('received_date', $today)->sum('quantity'),
            'outgoing_today'     => OutgoingTransaction::where('transaction_date', $today)->sum('quantity'),
            'revenue_today'      => OutgoingTransaction::where('transaction_date', $today)->sum('total_revenue'),
            'revenue_month'      => OutgoingTransaction::whereYear('transaction_date', $year)
                                        ->whereMonth('transaction_date', $month)->sum('total_revenue'),
            'gross_profit_month' => OutgoingTransaction::whereYear('transaction_date', $year)
                                        ->whereMonth('transaction_date', $month)->sum('gross_profit'),
            'reject_total'       => RejectItem::whereYear('reject_date', $year)
                                        ->whereMonth('reject_date', $month)->sum('quantity'),
        ];

        // ── Low stock (always current) ────────────────────────────────────
        $lowStockProducts = Product::whereColumn('stock_total', '<=', 'min_stock')
            ->where('is_active', true)
            ->with('category')
            ->orderBy('stock_total')
            ->take(10)
            ->get();

        // ── Build chart date list ─────────────────────────────────────────
        if ($isCurrentMonth) {
            $chartDates = collect(range(29, 0))
                ->map(fn($i) => now()->subDays($i)->toDateString());
            $chartLabels = collect(range(29, 0))
                ->map(fn($i) => now()->subDays($i)->format('d/m'))
                ->values()->all();
        } else {
            $daysInMonth = $selectedPeriod->daysInMonth;
            $chartDates  = collect(range(1, $daysInMonth))
                ->map(fn($d) => Carbon::createFromDate($year, $month, $d)->toDateString());
            $chartLabels = collect(range(1, $daysInMonth))
                ->map(fn($d) => sprintf('%02d', $d))
                ->values()->all();
        }

        // ── Sales chart ───────────────────────────────────────────────────
        $salesData = OutgoingTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(total_revenue) as revenue'),
                DB::raw('SUM(gross_profit) as profit')
            )
            ->when($isCurrentMonth,
                fn($q) => $q->where('transaction_date', '>=', now()->subDays(29)),
                fn($q) => $q->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartRevenue = $chartDates->map(fn($d) => (float) ($salesData[$d]->revenue ?? 0))->values()->all();
        $chartProfit  = $chartDates->map(fn($d) => (float) ($salesData[$d]->profit  ?? 0))->values()->all();

        // ── Cashflow chart ────────────────────────────────────────────────
        $cashflowData = FinancialTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw("SUM(CASE WHEN type='cash_in' THEN amount ELSE 0 END) as cash_in"),
                DB::raw("SUM(CASE WHEN type='cash_out' THEN amount ELSE 0 END) as cash_out")
            )
            ->when($isCurrentMonth,
                fn($q) => $q->where('transaction_date', '>=', now()->subDays(29)),
                fn($q) => $q->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $cashIn  = $chartDates->map(fn($d) => (float) ($cashflowData[$d]->cash_in  ?? 0))->values()->all();
        $cashOut = $chartDates->map(fn($d) => (float) ($cashflowData[$d]->cash_out ?? 0))->values()->all();

        // ── Recent transactions (filtered by selected period) ─────────────
        $recentTransactions = OutgoingTransaction::with('product', 'user')
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->latest()
            ->take(10)
            ->get();

        // ── Year list for selector ────────────────────────────────────────
        $minYear = (int) DB::table('outgoing_transactions')
            ->selectRaw('MIN(YEAR(transaction_date)) as y')
            ->value('y');
        $minYear        = $minYear ? min($minYear, now()->year) : now()->year;
        $availableYears = range(now()->year, $minYear);

        return view('dashboard.index', compact(
            'stats', 'lowStockProducts',
            'chartLabels', 'chartRevenue', 'chartProfit',
            'cashIn', 'cashOut', 'recentTransactions',
            'year', 'month', 'isCurrentMonth', 'selectedPeriod', 'availableYears'
        ));
    }
}