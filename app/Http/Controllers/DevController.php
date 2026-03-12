<?php

namespace App\Http\Controllers;

use App\Exports\QueryResultsExport;
use App\Http\Requests\DevExecuteRequest;
use App\Http\Requests\DevExportRequest;
use App\Http\Requests\DevIndexRequest;
use App\Models\SqlExecutionLog;
use App\Services\SqlExecutionLogService;
use App\Services\SqlExecutorService;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DevController extends Controller
{
    public function __construct(
        protected SqlExecutorService $sqlExecutorService,
        protected SqlExecutionLogService $sqlExecutionLogService
    ) {
        //
    }

    public function index(DevIndexRequest $request)
    {
        $validated = $request->validated();
        $executionId = isset($validated['execution_id']) ? (int) $validated['execution_id'] : null;
        $page = max(1, (int) ($validated['page'] ?? 1));
        $sql = '';
        $results = collect();
        $columns = [];
        $paginator = null;
        $errorMessage = $this->pullErrorMessage($request);

        if ($executionId !== null) {
            [$sql, $results, $columns, $paginator, $errorMessage] = $this->loadExecutionPage(
                request: $request,
                executionId: $executionId,
                page: $page,
                errorMessage: $errorMessage
            );
        }

        return view('dev.index', [
            'sql' => $sql,
            'executionId' => $executionId,
            'results' => $results,
            'columns' => $columns,
            'paginator' => $paginator,
            'errorMessage' => $errorMessage,
        ]);
    }

    public function execute(DevExecuteRequest $request): RedirectResponse
    {
        $sql = trim((string) $request->validated('sql'));
        $startedAt = microtime(true);
        $status = SqlExecutionLog::STATUS_FAILED;
        $errorMessage = null;
        $rowCount = null;
        $data = null;

        try {
            $this->sqlExecutorService->ensureSelectOnly($sql);
            $data = $this->sqlExecutorService->execute($sql);
            $status = SqlExecutionLog::STATUS_SUCCESS;
            $rowCount = $data['total'];
        } catch (Throwable $exception) {
            $errorMessage = $this->displayableError($exception);
            $status = $exception instanceof InvalidArgumentException
                ? SqlExecutionLog::STATUS_REJECTED
                : SqlExecutionLog::STATUS_FAILED;
        }

        try {
            $log = $this->sqlExecutionLogService->record(
                request: $request,
                sql: $sql,
                status: $status,
                executionTimeMs: $this->executionTimeMs($startedAt),
                errorMessage: $errorMessage,
                rowCount: $rowCount
            );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('dev.index')
                ->withInput(['sql' => $sql])
                ->with('errorMessage', $status === SqlExecutionLog::STATUS_SUCCESS
                    ? 'Query executed, but the audit log could not be written.'
                    : $this->mergeAuditFailureMessage($errorMessage));
        }

        if ($status !== SqlExecutionLog::STATUS_SUCCESS || $data === null) {
            return redirect()
                ->route('dev.index')
                ->withInput(['sql' => $sql])
                ->with('errorMessage', $errorMessage);
        }

        return redirect()
            ->route('dev.index', ['execution_id' => $log->id])
            ->with('executedPage', [
                'execution_id' => $log->id,
                'page' => 1,
                'rows' => $data['rows']->all(),
                'total' => $data['total'],
                'per_page' => $data['per_page'],
            ]);
    }

    public function exportJson(DevExportRequest $request): StreamedResponse|RedirectResponse
    {
        [$execution, $redirect] = $this->resolveExportExecution($request);

        if ($redirect) {
            return $redirect;
        }

        try {
            $rows = $this->sqlExecutorService->executeAll($execution->sql_text);
        } catch (Throwable $exception) {
            return $this->exportErrorRedirect($exception, $execution->id);
        }

        $filename = $this->buildExportFilename('json', $execution->id);

        return response()->streamDownload(function () use ($rows) {
            echo json_encode(
                $rows->all(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }, $filename, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    public function exportExcel(DevExportRequest $request): BinaryFileResponse|RedirectResponse
    {
        [$execution, $redirect] = $this->resolveExportExecution($request);

        if ($redirect) {
            return $redirect;
        }

        try {
            $rows = $this->sqlExecutorService->executeAll($execution->sql_text);
        } catch (Throwable $exception) {
            return $this->exportErrorRedirect($exception, $execution->id);
        }

        return Excel::download(
            new QueryResultsExport($rows),
            $this->buildExportFilename('xlsx', $execution->id)
        );
    }

    private function loadExecutionPage(
        Request $request,
        int $executionId,
        int $page,
        ?string $errorMessage
    ): array {
        $execution = $this->sqlExecutionLogService->findSuccessfulExecutionForUser(
            executionId: $executionId,
            userId: (int) $request->user()->id
        );

        if (! $execution) {
            return ['', collect(), [], null, 'Execution record not found.'];
        }

        $sql = trim((string) $execution->sql_text);
        $executedPage = $request->session()->get('executedPage');

        try {
            if ($this->canUseFlashedPage($executedPage, $executionId, $page)) {
                $data = [
                    'rows' => collect($executedPage['rows'] ?? []),
                    'total' => (int) ($executedPage['total'] ?? 0),
                    'page' => 1,
                    'per_page' => (int) ($executedPage['per_page'] ?? 50),
                ];
            } else {
                $data = $this->sqlExecutorService->execute($sql, $page);
            }

            $results = $data['rows'];
            $columns = $results->isNotEmpty() ? array_keys($results->first()) : [];
            $paginator = new LengthAwarePaginator(
                $results,
                $data['total'],
                $data['per_page'],
                $data['page'],
                [
                    'path' => route('dev.index'),
                    'query' => ['execution_id' => $executionId],
                ]
            );

            return [$sql, $results, $columns, $paginator, $errorMessage];
        } catch (Throwable $exception) {
            return [$sql, collect(), [], null, $this->displayableError($exception)];
        }
    }

    private function resolveExportExecution(DevExportRequest $request): array
    {
        $executionId = (int) $request->validated('execution_id');
        $execution = $this->sqlExecutionLogService->findSuccessfulExecutionForUser(
            executionId: $executionId,
            userId: (int) $request->user()->id
        );

        if ($execution) {
            return [$execution, null];
        }

        return [
            null,
            redirect()
                ->route('dev.index')
                ->with('errorMessage', 'Execution record not found.'),
        ];
    }

    private function exportErrorRedirect(Throwable $exception, int $executionId): RedirectResponse
    {
        return redirect()
            ->route('dev.index', ['execution_id' => $executionId])
            ->with('errorMessage', $this->displayableError($exception));
    }

    private function canUseFlashedPage(mixed $executedPage, int $executionId, int $page): bool
    {
        if (! is_array($executedPage)) {
            return false;
        }

        return (int) ($executedPage['execution_id'] ?? 0) === $executionId
            && (int) ($executedPage['page'] ?? 0) === $page;
    }

    private function executionTimeMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }

    private function displayableError(Throwable $exception): string
    {
        if ($exception instanceof QueryException && $exception->getPrevious()) {
            $message = trim($exception->getPrevious()->getMessage());

            if ($message !== '') {
                return $message;
            }
        }

        return trim($exception->getMessage());
    }

    private function mergeAuditFailureMessage(?string $errorMessage): string
    {
        $errorMessage = trim((string) $errorMessage);

        if ($errorMessage === '') {
            return 'The query could not be completed, and the audit log could not be written.';
        }

        return $errorMessage.' Audit log could not be written.';
    }

    private function pullErrorMessage(Request $request): ?string
    {
        $errorMessage = trim((string) $request->session()->pull('errorMessage', ''));

        return $errorMessage === '' ? null : $errorMessage;
    }

    private function buildExportFilename(string $extension, int $executionId): string
    {
        return sprintf(
            'sql-export-%d-%s.%s',
            $executionId,
            now()->format('Ymd_His'),
            $extension
        );
    }
}
