<?php

namespace Vstruhar\FilamentFailedJobs;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Vstruhar\FilamentFailedJobs\Resources\FailedJobsResource;

class FilamentFailedJobsPlugin implements Plugin
{
/**
     * Get the plugin identifier.
     */
    public function getId(): string
    {
        return 'filament-jobs-monitor';
    }

    /**
     * Register the plugin.
     */
    public function register(Panel $panel): void
    {
        $panel->resources([
            FailedJobsResource::class,
        ]);
    }

    /**
     * Boot the plugin.
     */
    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Make a new instance of the plugin.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Get the plugin instance.
     */
    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
