<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevExecuteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sql' => ['required', 'string'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('dev.index');
    }
}
