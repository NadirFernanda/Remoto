<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class PublicProjectsController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();
        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'published');
        }
        // Valor mínimo
        if ($request->filled('valor_min')) {
            $query->where('valor', '>=', $request->valor_min);
        }
        // Valor máximo
        if ($request->filled('valor_max')) {
            $query->where('valor', '<=', $request->valor_max);
        }
        // Data inicial
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        // Data final
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        // Tipo de negócio
        if ($request->filled('business_type')) {
            $query->whereRaw("JSON_EXTRACT(briefing, '$.business_type') LIKE ?", ['%' . $request->business_type . '%']);
        }
        // Público-alvo
        if ($request->filled('target_audience')) {
            $query->whereRaw("JSON_EXTRACT(briefing, '$.target_audience') LIKE ?", ['%' . $request->target_audience . '%']);
        }
        $projects = $query->orderByDesc('created_at')->paginate(12)->appends($request->all());
        return view('public-projects', compact('projects'));
    }
}
