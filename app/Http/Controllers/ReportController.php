<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\RejectItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockReportExport;
use App\Exports\SalesReportExport;
use App\Exports\FinancialReportExport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function stock(Request $request)
    {
        $products = Product::with(['category', 'batches' => fn($q) => $q->where('status', 'active')])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->get();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.stock-pdf', compact('products'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('laporan-stok-' . now()->format('Ymd') . '.pdf');
        }

        if ($request->format === 'excel') {
            return Excel::download(
                new StockReportExport($products),
                'laporan-stok-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('reports.stock', compact('products'));
    }

    public function sales(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        $transactions = OutgoingTransaction::with(['product.category', 'user', 'fifoDetails.batch'])
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->latest('transaction_date')
            ->get();

        $summary = [
            'total_revenue'    => $transactions->sum('total_revenue'),
            'total_hpp'        => $transactions->sum('total_hpp'),
            'gross_profit'     => $transactions->sum('gross_profit'),
            'total_qty'        => $transactions->sum('quantity'),
        ];

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.sales-pdf', compact('transactions', 'summary', 'dateFrom', 'dateTo'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('laporan-penjualan-' . now()->format('Ymd') . '.pdf');
        }

        if ($request->format === 'excel') {
            return Excel::download(
                new SalesReportExport($transactions, $summary),
                'laporan-penjualan-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('reports.sales', compact('transactions', 'summary', 'dateFrom', 'dateTo'));
    }

    public function fifo(Request $request)
    {
        $batches = ProductBatch::with(['product.category'])
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('received_date')
            ->get();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.fifo-pdf', compact('batches'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('laporan-fifo-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.fifo', compact('batches'));
    }

    public function reject(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        $rejects = RejectItem::with(['product', 'batch', 'user'])
            ->whereBetween('reject_date', [$dateFrom, $dateTo])
            ->latest('reject_date')
            ->get();

        $totalLoss = $rejects->sum('total_loss');

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.reject-pdf', compact('rejects', 'totalLoss', 'dateFrom', 'dateTo'))
                ->setPaper('a4');
            return $pdf->download('laporan-reject-' . now()->format('Ymd') . '.pdf');
        }

        return view('reports.reject', compact('rejects', 'totalLoss', 'dateFrom', 'dateTo'));
    }

    public function financial(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        $transactions = FinancialTransaction::with('user')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->orderBy('transaction_date')
            ->get();

        $totalIn  = $transactions->where('type', 'cash_in')->sum('amount');
        $totalOut = $transactions->where('type', 'cash_out')->sum('amount');
        $net      = $totalIn - $totalOut;

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.financial-pdf', compact(
                'transactions', 'totalIn', 'totalOut', 'net', 'dateFrom', 'dateTo'
            ))->setPaper('a4');
            return $pdf->download('laporan-keuangan-' . now()->format('Ymd') . '.pdf');
        }

        if ($request->format === 'excel') {
            return Excel::download(
                new FinancialReportExport($transactions),
                'laporan-keuangan-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('reports.financial', compact(
            'transactions', 'totalIn', 'totalOut', 'net', 'dateFrom', 'dateTo'
        ));
    }
}
