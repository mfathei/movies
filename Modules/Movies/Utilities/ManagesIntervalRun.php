<?php

declare(strict_types=1);

namespace Modules\Movies\Utilities;

use Illuminate\Support\Carbon;
use RuntimeException;

class ManagesIntervalRun
{
    public const KEY = 'movies_sync_interval';

    /** @var Carbon */
    protected $nextRun;

    public function checkNextRun()
    {
        $this->nextRun = $this->getNextRun();
        if ($this->nextRun->gt(now())) {
            throw new RuntimeException('Not yet');
        }
    }

    public function getNextRun(): Carbon
    {
        $lastExecutionTime = $this->getLastExecutionTime(self::KEY);

        return $lastExecutionTime->addMinutes((int) config('movies.interval_minutes'));
    }

    protected function getLastExecutionTime($path): ?Carbon
    {
        if (! $path) {
            return null;
        }

        return cache()->remember($path, 60 * 60 * 100, static function () {
            return now();
        });
    }

    public function setLastExecutionTime($path, Carbon $value): bool
    {
        if (! $path) {
            return false;
        }

        return cache()->put($path, $value, 60 * 60 * 100);
    }
}
