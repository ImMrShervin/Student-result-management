<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:run --only-db')->dailyAt('03:00')->onOneServer();
Schedule::command('activitylog:clean')->weekly();
Schedule::command('cache:prune-stale-tags')->hourly();
Schedule::command('srms:recompute-gpa')->dailyAt('02:00');
