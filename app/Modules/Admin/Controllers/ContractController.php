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
        $filters = [
            'q'               => trim((string) $request->query('q', '')),
            'type'            => trim((string) $request->query('type', '')),
            'status'          => trim((string) $request->query('status', '')),
            'start_date_from' => $request->query('start_date_from', ''),
            'start_date_to'   => $request->query('start_date_to', ''),
            'end_date_from'   => $request->query('end_date_from', ''),
            'end_date_to'     => $request->query('end_date_to', ''),
        ];

        $query = Contract::query();

        if ($filters['q'] !== '') {
            $query->where(function ($sub) use ($filters) {
                $sub->where('partner_name', 'LIKE', '%' . $filters['q'] . '%')
                    ->orWhere('type', 'LIKE', '%' . $filters['q'] . '%')
                    ->orWhere('notes', 'LIKE', '%' . $filters['q'] . '%');
            });
        }

        if ($filters['type'] !== '') {
            $query->where('type', 'LIKE', '%' . $filters['type'] . '%');
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['start_date_from'] !== '') {
            $query->whereDate('start_date', '>=', $filters['start_date_from']);
        }

        if ($filters['start_date_to'] !== '') {
            $query->whereDate('start_date', '<=', $filters['start_date_to']);
        }

        if ($filters['end_date_from'] !== '') {
            $query->whereDate('end_date', '>=', $filters['end_date_from']);
        }

        if ($filters['end_date_to'] !== '') {
            $query->whereDate('end_date', '<=', $filters['end_date_to']);
        }

        $statusBreakdown = (clone $query)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $contracts = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $statusTotals = [
            'ativo'     => $statusBreakdown['ativo'] ?? 0,
            'pendente'  => $statusBreakdown['pendente'] ?? 0,
            'encerrado' => $statusBreakdown['encerrado'] ?? 0,
        ];

        $types = Contract::select('type')->distinct()->orderBy('type')->pluck('type');

        return view('admin.contracts.index', compact('contracts', 'filters', 'statusTotals', 'types'));
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
