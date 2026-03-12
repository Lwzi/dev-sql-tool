<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SqlExecutorService
{
    private const PER_PAGE = 20;
    private const MAX_PER_PAGE = 100;
    private const MAX_TOTAL_ROWS = 5000;
    private const TIMEOUT_MS = 5000;

    public function ensureSelectOnly(string $sql): void
    {
        $normalized = ltrim(Str::lower($sql));

        if (! Str::startsWith($normalized, 'select')) {
            throw new \InvalidArgumentException('Only SELECT statements are allowed.');
        }

        $forbiddenKeywords = [
            'insert ',
            'update ',
            'delete ',
            'drop ',
            'truncate ',
            'alter ',
            'create ',
            'replace ',
            'rename ',
            'grant ',
            'revoke ',
            'commit',
            'rollback',
        ];

        foreach ($forbiddenKeywords as $keyword) {
            if (Str::contains($normalized, $keyword)) {
                throw new \InvalidArgumentException('Only SELECT statements are allowed.');
            }
        }

        if (Str::contains($normalized, ';')) {
            throw new \InvalidArgumentException('Multiple statements are not allowed.');
        }
    }

    public function execute(string $sql, int $page = 1, int $perPage = 20): array
    {
        $sql = trim($sql);
        $page = max(1, $page);
        $this->ensureSelectOnly($sql);

        $connection = $this->connection();
        $this->applyMySqlExecutionTimeout($connection);

        $perPage = $this->normalizePerPage($perPage);
        $total = $this->countRows($connection, $sql);
        $this->guardTotalRows($total);
        $rows = $this->fetchRows($connection, $sql, $page, $perPage);

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    private function connection(): Connection
    {
        return DB::connection();
    }

    private function normalizePerPage(int $perPage): int
    {
        $resolved = $perPage > 0 ? $perPage : self::PER_PAGE;

        return min($resolved, self::MAX_PER_PAGE, $this->maxTotalRows());
    }

    private function countRows(Connection $connection, string $sql): int
    {
        $countSql = sprintf('SELECT COUNT(*) AS aggregate FROM (%s) AS temp_table', $sql);

        return (int) ($connection->selectOne($countSql)->aggregate ?? 0);
    }

    private function guardTotalRows(int $total): void
    {
        $maxTotalRows = $this->maxTotalRows();

        if ($total <= $maxTotalRows) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            'Result set too large (%d rows). Maximum allowed is %d rows. Please narrow your query.',
            $total,
            $maxTotalRows
        ));
    }

    private function fetchRows(Connection $connection, string $sql, int $page, int $perPage)
    {
        $offset = max(0, ($page - 1) * $perPage);
        $pagedSql = sprintf('%s LIMIT %d OFFSET %d', $sql, $perPage, $offset);

        return collect($connection->select($pagedSql))
            ->map(fn ($row) => (array) $row)
            ->values();
    }

    private function maxTotalRows(): int
    {
        return self::MAX_TOTAL_ROWS;
    }

    private function applyMySqlExecutionTimeout(Connection $connection): void
    {
        if ($connection->getDriverName() !== 'mysql') {
            return;
        }

        $connection->unprepared('SET SESSION MAX_EXECUTION_TIME='.(int) self::TIMEOUT_MS);
    }
}
