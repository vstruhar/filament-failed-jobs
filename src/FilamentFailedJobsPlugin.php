<?php

namespace Vstruhar\FilamentFailedJobs;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Vstruhar\FilamentFailedJobs\Resources\FailedJobsResource;

class FilamentFailedJobsPlugin implements Plugin
{
    use EvaluatesClosures;

    /**
     * The resource navigation status.
     */
    protected bool|Closure $navigation = true;

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

    /**
     * Determine whether the resource navigation is enabled.
     */
    public function shouldRegisterNavigation(): bool
    {
        return $this->evaluate($this->navigation) === true ?? config('filament-failed-jobs.resources.enabled');
    }

    /**
     * Enable the resource navigation.
     */
    public function enableNavigation(bool|Closure $callback = true): static
    {
        $this->navigation = $callback;

        return $this;
    }
}
