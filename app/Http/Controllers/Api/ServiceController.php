<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::with('cliente:id,name', 'freelancer:id,name')
            ->where('status', 'published');

        if ($request->filled('search')) {
            $query->where('titulo', 'ilike', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $services = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json($services);
    }

    public function show(Service $service): JsonResponse
    {
        $service->load('cliente:id,name', 'freelancer:id,name', 'candidates.freelancer:id,name');

        return response()->json($service);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descricao'   => 'required|string',
            'valor'       => 'nullable|numeric|min:0',
            'prazo'       => 'nullable|date|after:today',
            'categoria'   => 'nullable|string|max:100',
        ]);

        $service = Service::create([
            ...$data,
            'cliente_id' => $request->user()->id,
            'status'     => 'published',
        ]);

        (new \App\Services\AffiliateService())->creditCommissionForReferredAction($request->user(), 'publish_service', $service->id);

        return response()->json($service, 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        if ($service->cliente_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $data = $request->validate([
            'titulo'    => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string',
            'valor'     => 'nullable|numeric|min:0',
            'prazo'     => 'nullable|date|after:today',
        ]);

        $service->update($data);

        return response()->json($service);
    }
}
