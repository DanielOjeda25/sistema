<?php

namespace App\Providers;

use App\Contracts\ProjectReportGenerator;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectReportGenerator::class, function ($app) {
            $generator = config('ai.generator');

            if (! is_string($generator) || ! is_a($generator, ProjectReportGenerator::class, true)) {
                throw new InvalidArgumentException(
                    'AI_REPORT_GENERATOR debe implementar ProjectReportGenerator.'
                );
            }

            return $app->make($generator);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
