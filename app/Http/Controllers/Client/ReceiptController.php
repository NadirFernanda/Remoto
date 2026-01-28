<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function download(Service $service)
    {
        $user = Auth::user();
        $html = view('livewire.client.receipt-pdf', compact('service', 'user'))->render();
        $pdf = \PDF::loadHTML($html);
        return $pdf->download('recibo_transacao_' . $service->id . '.pdf');
    }
}
