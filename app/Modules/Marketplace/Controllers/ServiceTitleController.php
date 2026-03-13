<?php

namespace App\Modules\Marketplace\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class ServiceTitleController
{
    public function update($serviceId, Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:100',
        ]);

        $service = Service::findOrFail($serviceId);

        if ($service->cliente_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $service->titulo = $request->input('titulo');
        $service->save();

        return response()->json(['success' => true]);
    }
}
