<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task;
use App\Observers\ActivityLogObserver;
use App\UseCases\ActivityLog\ActivityLogUseCase;
use App\Repositories\ActivityLog\ActivityLogRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ActivityLogRepository::class);
        $this->app->singleton(ActivityLogUseCase::class, function ($app) {
            return new ActivityLogUseCase($app->make(ActivityLogRepository::class));
        });
    }

    public function boot(): void
    {
        $repository = new ActivityLogRepository();
        $useCase = new ActivityLogUseCase($repository);
        Task::observe(new ActivityLogObserver($useCase));
    }
}
