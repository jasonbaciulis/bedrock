<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Support;

use Illuminate\Support\Facades\ParallelTesting;

/**
 * The console tests share one storage directory and one content tree, so every
 * path and entry id they touch carries this id. Without it, one process would
 * delete the fixtures another process is still using.
 */
final class ParallelWorker
{
    /**
     * Paratest process id, or "0" outside a parallel run.
     */
    public static function id(): string
    {
        return (string) (ParallelTesting::token() ?: 0);
    }
}
