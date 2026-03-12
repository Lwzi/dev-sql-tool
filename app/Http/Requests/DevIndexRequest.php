<?php

namespace App\Http\Requests;

use App\Services\SqlExecutorService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class DevIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(SqlExecutorService $sqlExecutorService): array
    {
        return [
            'sql' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, Closure $fail) use ($sqlExecutorService): void {
                    $sql = trim((string) $value);

                    if ($sql === '') {
                        return;
                    }

                    try {
                        $sqlExecutorService->ensureSelectOnly($sql);
                    } catch (\InvalidArgumentException $exception) {
                        $fail($exception->getMessage());
                    }
                },
            ],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('dev.index');
    }
}
