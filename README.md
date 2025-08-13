# Filament plugin for managing failed jobs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vstruhar/filament-failed-jobs.svg?style=flat-square)](https://packagist.org/packages/vstruhar/filament-failed-jobs)
[![Total Downloads](https://img.shields.io/packagist/dt/vstruhar/filament-failed-jobs.svg?style=flat-square)](https://packagist.org/packages/vstruhar/filament-failed-jobs)



The Laravel Filament plugin for managing failed jobs offers a streamlined interface to monitor, retry, and delete failed jobs directly from the admin panel.

**This version is compatible with Filament v4. For Filament v3 compatibility, use version 1.x.**

<img width="100%" src="https://github.com/user-attachments/assets/8cd045d7-b23a-46e4-977a-47117decdcd0">

<img width="100%" src="https://github.com/user-attachments/assets/ad6c6139-91cd-4b77-9047-2a66878b07f0">

## Features
- Retry or delete all failed jobs
- Retry or delete single failed job
- Retry or delete selected failed jobs
- Retry or delete filtered failed jobs
- Can view details of the failed job with exception stack trace and models with ids

## Requirements

- PHP 8.2+
- Laravel 10.0+ 
- Filament 4.0+

## Installation

You can install the package via composer:

```bash
composer require vstruhar/filament-failed-jobs
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-failed-jobs-config"
```

This is the contents of the published config file:

```php
return [
    'resources' => [
        'enabled' => true,
        'label' => 'Failed job',
        'plural_label' => 'Failed jobs',
        'navigation_group' => 'Settings',
        'navigation_icon' => 'heroicon-o-exclamation-triangle',
        'navigation_sort' => null,
        'navigation_count_badge' => false,
    ],
];
```

## Usage

Add `FilamentFailedJobsPlugin` to plugins array in `AdminPanelProvider.php`.

```php
    // AdminPanelProvider.php
    ->plugins([
        // ...
        FilamentFailedJobsPlugin::make(),
    ])
```

Optionally you can chain `enableNavigation` method and add logic when to enable navigation button in the main sidebar.

```php
    // AdminPanelProvider.php
    ->plugins([
        // ...
        FilamentFailedJobsPlugin::make(),
            ->enableNavigation(fn() => auth()->user()->role === 'admin'),
    ])
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Vladimir Struhar](https://github.com/vstruhar)
- Inspired by [filament-jobs-monitor](https://github.com/croustibat/filament-jobs-monitor)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
