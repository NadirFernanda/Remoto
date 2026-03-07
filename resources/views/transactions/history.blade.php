@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Histórico de Transações</h1>
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-2">Como Cliente</h2>
        <table class="orders-table w-full mb-6">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Recibo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asClient as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td>{{ $service->titulo }}</td>
                        <td>{{ ucfirst($service->status) }}</td>
                        <td>Kz {{ number_format($service->valor, 2, ',', '.') }}</td>
                        <td>{{ $service->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($service->is_payment_released)
                                <a href="{{ url('storage/invoices/invoice_service_' . $service->id . '.pdf') }}" target="_blank" class="text-cyan-700 underline">Recibo</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-[#888]">Nenhuma transação encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>
        <h2 class="text-lg font-semibold mb-2">Como Freelancer</h2>
        <table class="orders-table w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Status</th>
                    <th>Valor Líquido</th>
                    <th>Data</th>
                    <th>Recibo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asFreelancer as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td>{{ $service->titulo }}</td>
                        <td>{{ ucfirst($service->status) }}</td>
                        <td>Kz {{ number_format($service->valor_liquido, 2, ',', '.') }}</td>
                        <td>{{ $service->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($service->is_payment_released)
                                <a href="{{ url('storage/invoices/invoice_service_' . $service->id . '.pdf') }}" target="_blank" class="text-cyan-700 underline">Recibo</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-[#888]">Nenhuma transação encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
