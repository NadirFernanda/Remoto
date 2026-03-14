<?php

namespace App\Modules\Admin\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UsersExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $query = User::query()->with('freelancerProfile');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $users    = $query->orderByDesc('created_at')->get();
        $filename = 'utilizadores_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nome', 'Email', 'Papel', 'KYC', 'Email Verificado', 'Criado Em']);

            foreach ($users as $user) {
                $kycStatus = $user->freelancerProfile->kyc_status ?? 'N/A';
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $kycStatus,
                    $user->email_verified_at ? 'Sim' : 'Não',
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
