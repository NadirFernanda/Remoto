<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $contracts = Contract::orderByDesc('created_at')->paginate(15);
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
            'type' => 'required|string|max:100',
            'status' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'documento' => 'nullable|file|mimes:pdf|max:8192',
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
            'type' => 'required|string|max:100',
            'status' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'documento' => 'nullable|file|mimes:pdf|max:8192',
        ]);
        if ($request->hasFile('documento')) {
            $data['document_path'] = $request->file('documento')->store('contratos', 'public');
        }
        $contract->update($data);
        return redirect()->route('admin.comercial.index')->with('success', 'Contrato/parceria atualizado!');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('admin.comercial.index')->with('success', 'Contrato/parceria removido!');
    }
}
