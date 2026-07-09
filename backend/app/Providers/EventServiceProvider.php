<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\GradePublished;
use App\Listeners\NotifyStudentOfGrade;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        GradePublished::class => [
            NotifyStudentOfGrade::class,
        ],
    ];
}
