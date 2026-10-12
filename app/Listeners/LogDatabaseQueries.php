<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class LogDatabaseQueries
{
    public function handle(QueryExecuted $event): void
    {
        Log::info('SQL Query Audit', [
            'sql' => $event->sql,
            'bindings' => $event->bindings,
            'time_ms' => $event->time,
        ]);
    }
}
