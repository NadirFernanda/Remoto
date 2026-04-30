<?php

namespace App\Modules\Admin\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AdminReceipt;

class AdminReceiptController extends Controller
{
    public function index()
    {
        $receipts = AdminReceipt::with('creator')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.receipts.index', compact('receipts'));
    }

    public function create()
    {
        return view('admin.receipts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'       => 'nullable|string|max:255',
            'nif'        => 'nullable|string|max:50',
            'telefone'   => 'nullable|string|max:30',
            'endereco'   => 'nullable|string|max:500',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
            'documento'  => 'nullable|file|mimes:pdf|max:8192',
        ]);

        if ($request->hasFile('documento')) {
            $data['document_path'] = $request->file('documento')->store('recibos-admin', 'public');
        }

        $data['receipt_number'] = AdminReceipt::generateNumber();
        $data['created_by']     = Auth::id();

        $receipt = AdminReceipt::create($data);

        return redirect()->route('admin.recibos.show', $receipt)->with('success', 'Recibo gerado com sucesso!');
    }

    public function show(AdminReceipt $recibo)
    {
        return view('admin.receipts.show', compact('recibo'));
    }

    public function destroy(AdminReceipt $recibo)
    {
        if ($recibo->document_path) {
            Storage::disk('public')->delete($recibo->document_path);
        }
        $recibo->delete();

        return redirect()->route('admin.recibos.index')->with('success', 'Recibo eliminado.');
    }
}
