<?php

namespace App\Services;

use App\Models\SqlExecutionLog;
use Illuminate\Http\Request;

class SqlExecutionLogService
{
    public function record(
        Request $request,
        string $sql,
        string $status,
        int $executionTimeMs,
        ?string $errorMessage = null,
        ?int $rowCount = null
    ): SqlExecutionLog {
        return SqlExecutionLog::query()->create([
            'user_id' => $request->user()?->id,
            'sql_text' => trim($sql),
            'executed_at' => now(),
            'status' => $status,
            'error_message' => $this->normalizeMessage($errorMessage),
            'execution_time_ms' => max(0, $executionTimeMs),
            'row_count' => $rowCount,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function findSuccessfulExecutionForUser(int $executionId, int $userId): ?SqlExecutionLog
    {
        return SqlExecutionLog::query()
            ->whereKey($executionId)
            ->where('user_id', $userId)
            ->where('status', SqlExecutionLog::STATUS_SUCCESS)
            ->first();
    }

    private function normalizeMessage(?string $message): ?string
    {
        $message = trim((string) $message);

        return $message === '' ? null : $message;
    }
}
