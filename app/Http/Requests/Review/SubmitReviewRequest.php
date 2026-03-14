<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|integer|exists:services,id',
            'target_id'  => 'required|integer|exists:users,id|different:' . (auth()->id() ?? 0),
            'rating'     => 'required|integer|between:1,5',
            'comment'    => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'O projecto é obrigatório.',
            'service_id.exists'   => 'Projecto não encontrado.',
            'target_id.required'  => 'O utilizador a avaliar é obrigatório.',
            'target_id.exists'    => 'Utilizador não encontrado.',
            'target_id.different' => 'Não pode avaliar a si mesmo.',
            'rating.required'     => 'Selecione uma classificação.',
            'rating.between'      => 'A classificação deve ser entre 1 e 5 estrelas.',
            'comment.max'         => 'O comentário não pode exceder 2000 caracteres.',
        ];
    }
}
