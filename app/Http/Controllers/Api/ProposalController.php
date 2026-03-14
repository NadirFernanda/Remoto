<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use App\Models\ServiceCandidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProposalController extends Controller
{
    /**
     * List all proposals for a service (client only).
     */
    public function index(Request $request, Service $service): JsonResponse
    {
        if ($service->cliente_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $proposals = $service->candidates()
            ->with('freelancer:id,name,email')
            ->get()
            ->map(fn($c) => [
                'id'               => $c->id,
                'freelancer'       => $c->freelancer,
                'status'           => $c->status,
                'proposal_message' => $c->proposal_message,
                'proposal_value'   => $c->proposal_value,
                'created_at'       => $c->created_at,
            ]);

        return response()->json($proposals);
    }

    /**
     * Submit a proposal for a service (freelancer only).
     */
    public function store(Request $request, Service $service): JsonResponse
    {
        $user = $request->user();

        if ($service->cliente_id === $user->id) {
            abort(403, 'Não pode candidatar-se ao seu próprio projeto.');
        }

        if ($service->status !== 'published') {
            abort(422, 'Este projeto não aceita candidaturas no momento.');
        }

        if ($service->candidates()->count() >= 6) {
            abort(422, 'Este projeto já atingiu o limite de candidatos.');
        }

        $data = $request->validate([
            'proposal_message' => 'required|string|max:2000',
            'proposal_value'   => 'nullable|numeric|min:0',
        ]);

        $existing = $service->candidates()->where('freelancer_id', $user->id)->first();

        if ($existing) {
            abort(422, 'Já enviou uma proposta para este projeto.');
        }

        $candidate = $service->candidates()->create([
            'freelancer_id'    => $user->id,
            'status'           => 'pending',
            'proposal_message' => $data['proposal_message'],
            'proposal_value'   => $data['proposal_value'] ?? null,
        ]);

        return response()->json($candidate, 201);
    }

    /**
     * List proposals submitted by the authenticated freelancer.
     */
    public function mine(Request $request): JsonResponse
    {
        $proposals = ServiceCandidate::with('service:id,titulo,status,cliente_id')
            ->where('freelancer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($proposals);
    }
}
