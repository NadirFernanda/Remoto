<?php

namespace App\Modules\Admin\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AdminReceipt;
use App\Models\CreatorSubscription;
use App\Models\InfoprodutoCompra;
use App\Models\Service;
use App\Models\User;

class AdminReceiptController extends Controller
{
    public function index(Request $request)
    {
        $receipts = AdminReceipt::with('creator', 'user')
            ->orderByDesc('created_at')
            ->paginate(15);

        $paid = ['published', 'accepted', 'negotiating', 'in_progress', 'em_andamento',
                 'em andamento', 'delivered', 'revision_requested', 'completed', 'concluido',
                 'cancelled', 'cancelado'];

        $serviceSearch = $request->input('sq', '');

        $servicesQuery = Service::with('cliente', 'freelancer')
            ->whereIn('status', $paid)
            ->orderByDesc('created_at');

        if ($serviceSearch) {
            $servicesQuery->where(function ($q) use ($serviceSearch) {
                $q->where('titulo', 'like', "%{$serviceSearch}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('name', 'like', "%{$serviceSearch}%"));
            });
        }

        $services = $servicesQuery->paginate(10, ['*'], 'sp');

        return view('admin.receipts.index', compact('receipts', 'services', 'serviceSearch'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.receipts.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'nullable|exists:users,id',
            'valor'      => 'nullable|numeric|min:0',
            'nome'       => 'nullable|string|max:255',
            'nif'        => 'nullable|string|max:50',
            'telefone'   => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:255',
            'endereco'   => 'nullable|string|max:500',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
            'documento'  => 'nullable|file|mimes:pdf|max:8192',
        ]);

        // Auto-fill from selected user if not manually overridden
        if (!empty($data['user_id'])) {
            $user = User::find($data['user_id']);
            if ($user) {
                $data['nome']  = $data['nome']  ?: $user->name;
                $data['email'] = $data['email'] ?: $user->email;
            }
        }

        if ($request->hasFile('documento')) {
            $data['document_path'] = $request->file('documento')->store('recibos-admin', 'public');
        }

        unset($data['documento']);
        $data['receipt_number'] = AdminReceipt::generateNumber();
        $data['created_by']     = Auth::id();

        $receipt = AdminReceipt::create($data);

        return redirect()->route('admin.recibos.show', $receipt)->with('success', 'Recibo gerado com sucesso!');
    }

    public function show(AdminReceipt $recibo)
    {
        $recibo->loadMissing('user', 'creator');
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

    public function serviceReceipt(Service $service)
    {
        $service->loadMissing('cliente', 'freelancer');
        $user = $service->cliente;

        return response()
            ->view('livewire.client.receipt-pdf', compact('service', 'user'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function subscriptionReceipt(CreatorSubscription $subscription)
    {
        $subscription->loadMissing('subscriber', 'creator');

        return response()
            ->view('admin.receipts.subscription-receipt-pdf', compact('subscription'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function infoprodutoReceipt(InfoprodutoCompra $compra)
    {
        $compra->loadMissing('comprador', 'infoproduto.user');

        return response()
            ->view('admin.receipts.infoproduto-receipt-pdf', compact('compra'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function bulkReceipts(Request $request)
    {
        $ids      = array_filter(explode(',', $request->input('ids', '')));
        $services = Service::with('cliente', 'freelancer')
            ->whereIn('id', $ids)
            ->get();

        return view('admin.receipts.bulk-print', compact('services'));
    }
}
