<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SqlExecutorService
{
    private const PER_PAGE = 50;
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

    public function execute(string $sql, int $page = 1): array
    {
        $sql = trim($sql);
        $page = max(1, $page);
        $this->ensureSelectOnly($sql);

        $connection = $this->connection();
        $this->applyMySqlExecutionTimeout($connection);

        $total = $this->countRows($connection, $sql);
        $this->guardTotalRows($total);
        $rows = $this->fetchRows($connection, $sql, $page);

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => self::PER_PAGE,
        ];
    }

    private function connection(): Connection
    {
        return DB::connection();
    }

    private function countRows(Connection $connection, string $sql): int
    {
        $countSql = sprintf('select count(*) as aggregate from (%s) as temp_count', $sql);

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

    private function fetchRows(Connection $connection, string $sql, int $page)
    {
        $offset = max(0, ($page - 1) * self::PER_PAGE);
        $pagedSql = sprintf('select * from (%s) as temp_result limit ? offset ?', $sql);

        return collect($connection->select($pagedSql, [self::PER_PAGE, $offset]))
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
