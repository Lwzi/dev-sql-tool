<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'execution_id' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('dev.index');
    }
}
