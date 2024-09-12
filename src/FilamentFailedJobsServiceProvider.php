<?php

namespace Vstruhar\FilamentFailedJobs;

use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vstruhar\FilamentFailedJobs\Testing\TestsFilamentFailedJobs;

class FilamentFailedJobsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-failed-jobs';

    public static string $viewNamespace = 'filament-failed-jobs';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        // if (file_exists($package->basePath('/../database/migrations'))) {
        //     $package->hasMigrations($this->getMigrations());
        // }

        // if (file_exists($package->basePath('/../resources/lang'))) {
        //     $package->hasTranslations();
        // }

        // if (file_exists($package->basePath('/../resources/views'))) {
        //     $package->hasViews(static::$viewNamespace);
        // }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Testing
        Testable::mixin(new TestsFilamentFailedJobs);
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [];
    }
}
