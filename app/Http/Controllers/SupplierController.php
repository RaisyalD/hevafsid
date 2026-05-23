<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('incomingTransactions')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('city', 'like', "%{$request->search}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        $count        = Supplier::withTrashed()->count() + 1;
        $data['code'] = 'SUP-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        Supplier::create($data);
        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:100',
            'is_active'      => 'boolean',
            'notes'          => 'nullable|string|max:500',
        ]);

        $supplier->update($data);
        return back()->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->incomingTransactions()->exists()) {
            return back()->with('error', 'Supplier memiliki transaksi dan tidak dapat dihapus.');
        }
        $supplier->delete();
        return back()->with('success', 'Supplier berhasil dihapus.');
    }
}
