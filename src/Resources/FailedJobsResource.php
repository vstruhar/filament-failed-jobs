<?php

namespace Vstruhar\FilamentFailedJobs\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Vstruhar\FilamentFailedJobs\Models\FailedJob;
use Vstruhar\FilamentFailedJobs\Resources\FailedJobsResource\Pages\ListFailedJobs;

class FailedJobsResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('uuid')
                    ->readOnly(),
                TextInput::make('queue')
                    ->readOnly(),
                DateTimePicker::make('failed_at')
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID'),
                TextColumn::make('queue')
                    ->sortable(),
                TextColumn::make('exception')
                    ->formatStateUsing(fn ($record): string => (string) str($record->exception)->before(': ')),
                TextColumn::make('message')
                    ->formatStateUsing(fn ($record): string => (string) str($record->exception)->between(': ', 'Stack trace:')),
                TextColumn::make('failed_at')
                    ->label('Failed at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('failed_at', 'desc')
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        fn () => FailedJob::group('queue')
                            ->get()
                            ->pluck('queue')
                            ->mapWithKeys(fn ($queue) => [$queue => Str::title($queue)])
                    )
                    ->query(function (Builder $query, array $data) {
                        return $query->where('queue', $data['value']);
                    }),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return config('filament-failed-jobs.resources.navigation_count_badge', false)
            ? number_format(static::getModel()::count())
            : null;
    }

    public static function getModelLabel(): string
    {
        return config('filament-failed-jobs.resources.label', 'Failed job');
    }

    public static function getPluralModelLabel(): string
    {
        return config('filament-failed-jobs.resources.plural_label', 'Failed jobs');
    }

    public static function getNavigationLabel(): string
    {
        return Str::title(static::getPluralModelLabel()) ?? Str::title(static::getModelLabel());
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-failed-jobs.resources.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-failed-jobs.resources.navigation_sort');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-failed-jobs.resources.enabled');
    }

    public static function getNavigationIcon(): string
    {
        return config('filament-failed-jobs.resources.navigation_icon');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFailedJobs::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }
}
