<?php

namespace App\Http\Controllers;

use App\Http\Requests\DevIndexRequest;
use App\Services\SqlExecutorService;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class DevController extends Controller
{
    public function __construct(
        protected SqlExecutorService $sqlExecutorService
    ) {
        //
    }

    public function index(DevIndexRequest $request)
    {
        $validated = $request->validated();
        $sql = trim((string) ($validated['sql'] ?? ''));
        $page = max(1, (int) ($validated['page'] ?? 1));
        $results = collect();
        $columns = [];
        $paginator = null;
        $errorMessage = null;

        if ($sql !== '') {
            try {
                $data = $this->sqlExecutorService->execute($sql, $page);
                $results = $data['rows'];
                $columns = $results->isNotEmpty() ? array_keys($results->first()) : [];
                $paginator = new LengthAwarePaginator(
                    $results,
                    $data['total'],
                    $data['per_page'],
                    $data['page'],
                    [
                        'path' => route('dev.index'),
                        'query' => ['sql' => $sql],
                    ]
                );
            } catch (Throwable $exception) {
                $errorMessage = $exception->getMessage();
            }
        }

        return view('dev.index', [
            'sql' => $sql,
            'results' => $results,
            'columns' => $columns,
            'paginator' => $paginator,
            'errorMessage' => $errorMessage,
        ]);
    }
}
