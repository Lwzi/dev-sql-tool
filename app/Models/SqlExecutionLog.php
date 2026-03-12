<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlExecutionLog extends Model
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REJECTED = 'rejected';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'sql_text',
        'executed_at',
        'status',
        'error_message',
        'execution_time_ms',
        'row_count',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'execution_time_ms' => 'integer',
        'row_count' => 'integer',
    ];
}
