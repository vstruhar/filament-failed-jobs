# Filament plugin for managing failed jobs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vstruhar/filament-failed-jobs.svg?style=flat-square)](https://packagist.org/packages/vstruhar/filament-failed-jobs)
[![Total Downloads](https://img.shields.io/packagist/dt/vstruhar/filament-failed-jobs.svg?style=flat-square)](https://packagist.org/packages/vstruhar/filament-failed-jobs)



The Laravel Filament plugin for managing failed jobs offers a streamlined interface to monitor, retry, and delete failed jobs directly from the admin panel.

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

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Vladimir Struhar](https://github.com/vstruhar)
- Inspired by [filament-jobs-monitor](https://github.com/croustibat/filament-jobs-monitor)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
