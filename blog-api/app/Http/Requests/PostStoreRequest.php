<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // доступ контролируем полисами на update/delete
    }

    public function rules(): array
    {
        return [
            'status' => ['required','string','in:active,draft,archived'],
            'body'   => ['required','string','min:1'],
        ];
    }
}
