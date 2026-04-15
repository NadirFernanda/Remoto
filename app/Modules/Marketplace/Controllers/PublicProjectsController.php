<?php

namespace App\Modules\Marketplace\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Service;

class PublicProjectsController extends Controller
{
    public function index(Request $request)
    {
        // Cache keyed por hash dos filtros — 3 min
        // Garante que cada combinação única de filtros tem a sua própria entrada
        $filters = $request->only(['status','valor_min','valor_max','data_inicio','data_fim','business_type','target_audience','page']);
        $cacheKey = 'public_projects:' . md5(serialize($filters));

        $projects = Cache::remember($cacheKey, 180, function () use ($request) {
            $query = Service::query();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', 'published');
            }

            if ($request->filled('valor_min')) {
                $query->where('valor', '>=', $request->valor_min);
            }
            if ($request->filled('valor_max')) {
                $query->where('valor', '<=', $request->valor_max);
            }
            if ($request->filled('data_inicio')) {
                $query->whereDate('created_at', '>=', $request->data_inicio);
            }
            if ($request->filled('data_fim')) {
                $query->whereDate('created_at', '<=', $request->data_fim);
            }
            if ($request->filled('business_type')) {
                $query->whereRaw("JSON_EXTRACT(briefing, '$.business_type') LIKE ?", ['%' . $request->business_type . '%']);
            }
            if ($request->filled('target_audience')) {
                $query->whereRaw("JSON_EXTRACT(briefing, '$.target_audience') LIKE ?", ['%' . $request->target_audience . '%']);
            }

            return $query->orderByDesc('created_at')->paginate(12)->appends($request->all());
        });

        return view('public-projects', compact('projects'));
    }

    public function show(Request $request, Service $service)
    {
        $service->loadMissing('cliente');
        return view('public-project-show', compact('service'));
    }
}
