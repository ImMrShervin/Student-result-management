<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\GpaCalculatorContract;
use App\Contracts\GradeCalculatorContract;
use App\Contracts\StudentRepositoryContract;
use App\Repositories\StudentRepository;
use App\Services\GpaCalculator;
use App\Services\GradeCalculator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GradeCalculatorContract::class, GradeCalculator::class);
        $this->app->bind(GpaCalculatorContract::class, GpaCalculator::class);
        $this->app->bind(StudentRepositoryContract::class, StudentRepository::class);
    }

    public function boot(): void
    {
        $this->app['url']->forceRootUrl(config('app.url'));

        if (in_array(request()->header('X-Locale'), ['en', 'fa'])) {
            app()->setLocale(request()->header('X-Locale'));
        }
    }
}
