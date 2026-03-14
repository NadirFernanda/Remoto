<?php

namespace App\Http\Requests\Dispute;

use Illuminate\Foundation\Http\FormRequest;

class OpenDisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'service_id'  => 'required|integer|exists:services,id',
            'reason'      => 'required|in:atraso,qualidade,nao_pagamento,outro',
            'description' => 'required|string|min:20|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required'  => 'O projecto é obrigatório.',
            'service_id.exists'    => 'Projecto não encontrado.',
            'reason.required'      => 'Selecione um motivo para a disputa.',
            'reason.in'            => 'Motivo inválido.',
            'description.required' => 'Descreva o problema em detalhe.',
            'description.min'      => 'A descrição deve ter pelo menos 20 caracteres.',
            'description.max'      => 'A descrição não pode exceder 2000 caracteres.',
        ];
    }
}
