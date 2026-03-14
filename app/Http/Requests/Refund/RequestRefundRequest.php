<?php

namespace App\Http\Requests\Refund;

use Illuminate\Foundation\Http\FormRequest;

class RequestRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|integer|exists:services,id',
            'reason'     => 'required|string|min:10|max:500',
            'details'    => 'nullable|string|max:2000',
            'evidence'   => 'nullable|array|max:3',
            'evidence.*' => 'file|mimetypes:image/jpeg,image/png,image/gif,application/pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required'  => 'O projecto é obrigatório.',
            'service_id.exists'    => 'Projecto não encontrado.',
            'reason.required'      => 'O motivo do reembolso é obrigatório.',
            'reason.min'           => 'Descreva o motivo com pelo menos 10 caracteres.',
            'reason.max'           => 'O motivo não pode exceder 500 caracteres.',
            'evidence.max'         => 'Pode enviar no máximo 3 ficheiros de evidência.',
            'evidence.*.max'       => 'Cada ficheiro não pode ultrapassar 5 MB.',
            'evidence.*.mimetypes' => 'Apenas imagens (JPG, PNG, GIF) e PDF são permitidos.',
        ];
    }
}
