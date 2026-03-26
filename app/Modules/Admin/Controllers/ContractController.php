<?php

namespace App\Modules\Admin\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::query();

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('partner_name', 'ilike', '%' . $q . '%')
                    ->orWhere('type', 'ilike', '%' . $q . '%')
                    ->orWhere('notes', 'ilike', '%' . $q . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $contracts = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('admin.contracts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'partner_name' => 'required|string|max:255',
            'type'         => 'required|string|max:100',
            'status'       => 'required|string',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date',
            'notes'        => 'nullable|string',
            'documento'    => 'nullable|file|mimes:pdf|max:8192',
        ]);

        if ($request->hasFile('documento')) {
            $data['document_path'] = $request->file('documento')->store('contratos', 'public');
        }

        Contract::create($data);
        return redirect()->route('admin.comercial.index')->with('success', 'Contrato/parceria cadastrado!');
    }

    public function show(Contract $contract)
    {
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        return view('admin.contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'partner_name' => 'required|string|max:255',
            'type'         => 'required|string|max:100',
            'status'       => 'required|string',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date',
            'notes'        => 'nullable|string',
            'documento'    => 'nullable|file|mimes:pdf|max:8192',
        ]);

        if ($request->hasFile('documento')) {
            if ($contract->document_path && Storage::disk('public')->exists($contract->document_path)) {
                Storage::disk('public')->delete($contract->document_path);
            }
            $data['document_path'] = $request->file('documento')->store('contratos', 'public');
        }

        $contract->update($data);
        return redirect()->route('admin.comercial.index')->with('success', 'Contrato/parceria atualizado!');
    }

    public function destroy(Contract $contract)
    {
        if ($contract->document_path && Storage::disk('public')->exists($contract->document_path)) {
            Storage::disk('public')->delete($contract->document_path);
        }
        $contract->delete();
        return redirect()->route('admin.comercial.index')->with('success', 'Contrato/parceria removido!');
    }
}
