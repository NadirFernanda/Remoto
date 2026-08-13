<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Recebe relatórios de violação da Content-Security-Policy-Report-Only
 * (auditoria de segurança, F3 — fase de monitorização antes de bloquear).
 *
 * Só regista — não faz nada com os dados além de log, para depois analisarmos
 * quais origens legítimas faltam na política antes de a tornar bloqueante.
 */
class CspReportController extends Controller
{
    public function store(Request $request): Response
    {
        $report = $request->input('csp-report', $request->all());

        Log::channel(config('logging.default'))->info('CSP violation report', ['report' => $report]);

        return response()->noContent();
    }
}
