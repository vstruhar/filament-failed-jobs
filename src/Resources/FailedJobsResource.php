<?php

namespace Vstruhar\FilamentFailedJobs\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Livewire\Component;
use Novadaemon\FilamentPrettyJson\Form\PrettyJsonField;
use Vstruhar\FilamentFailedJobs\FilamentFailedJobsPlugin;
use Vstruhar\FilamentFailedJobs\Models\FailedJob;
use Vstruhar\FilamentFailedJobs\Resources\FailedJobsResource\Pages\ListFailedJobs;

class FailedJobsResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->readOnly(),
                DateTimePicker::make('failed_at')
                    ->readOnly(),
                TextInput::make('connection')
                    ->readOnly(),
                TextInput::make('queue')
                    ->readOnly(),
                KeyValue::make('models')
                    ->addable(false)
                    ->deletable(false)
                    ->editableKeys(false)
                    ->editableValues(false)
                    ->keyLabel('Variable')
                    ->valueLabel('Model')
                    ->formatStateUsing(fn (Model $record) => $record->getModels()->mapWithKeys(fn ($value, $key) => ['$' . $key => $value])->toArray())
                    ->hidden(fn (Model $record) => $record->getModels()->isEmpty()),
                PrettyJsonField::make('payload')->columnSpanFull(),
                ViewField::make('exception')
                    ->view('filament-failed-jobs::exception-field')
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('exception')
                    ->formatStateUsing(fn (Model $record): string => $record->exceptionClass()),
                TextColumn::make('message')
                    ->wrap()
                    ->state(fn (Model $record): string => $record->exceptionMessage()),
                /*TextColumn::make('models')
                    ->default('-')
                    ->state(fn (Model $record): string => $record->getModels()->implode('<br>'))
                    ->html(),*/
                TextColumn::make('connection')
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('queue')
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('failed_at')
                    ->label('Failed at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('failed_at', 'desc')
            ->toolbarActions([
                BulkAction::make('retry')
                    ->label('Retry selected')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (Collection $records, Component $livewire) {
                        $records->each->retry();
                        $livewire->resetTable();
                    }),
                DeleteBulkAction::make(),
            ])
            ->headerActions([
                Action::make('retry_filtered')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (Component $livewire) {
                        $livewire->getFilteredTableQuery()->chunkById(50, fn ($records) => $records->each->retry());
                        $livewire->resetTable();
                    })
                    ->visible(fn (Component $livewire) => collect($livewire->tableFilters)->pluck('value')->filter()->isNotEmpty()),
                ActionGroup::make([
                    Action::make('retry_all')
                        ->icon('heroicon-m-arrow-path')
                        ->requiresConfirmation()
                        ->action(function (Component $livewire) {
                            Artisan::call('queue:retry', ['id' => 'all']);
                            $livewire->resetTable();
                        }),
                    Action::make('delete_all')
                        ->color('danger')
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to delete all failed jobs?')
                        ->action(function (Component $livewire) {
                            Artisan::call('queue:flush');
                            $livewire->resetTable();
                        }),
                ]),
            ])
            ->recordActions([
                Action::make('retry')
                    ->button()
                    ->icon('heroicon-m-arrow-path')
                    ->action(function (Model $record, Component $livewire) {
                        $record->retry();
                        $livewire->resetTable();
                    }),
                ViewAction::make()
                    ->button()
                    ->modalWidth(config('filament-failed-jobs.modal-width')),
            ])
            ->filters([
                SelectFilter::make('queue')
                    ->options(
                        fn () => FailedJob::distinct()
                            ->pluck('queue')
                            ->mapWithKeys(fn ($value) => [$value => Str::title($value)])
                    )
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            return $query->where('queue', $data['value']);
                        }
                    }),
                SelectFilter::make('connection')
                    ->options(
                        fn () => FailedJob::distinct()
                            ->pluck('connection')
                            ->mapWithKeys(fn ($value) => [$value => Str::title($value)])
                    )
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            return $query->where('connection', $data['value']);
                        }
                    }),
                SelectFilter::make('failed_at')
                    ->options([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This week',
                        'last_week' => 'Last week',
                        'this_month' => 'This month',
                        'last_month' => 'Last month',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            if ($data['value'] === 'today') {
                                $query->where('failed_at', '>=', now()->startOfDay());
                            } elseif ($data['value'] === 'yesterday') {
                                $query->where('failed_at', '>=', now()->subDay()->startOfDay())
                                    ->where('failed_at', '<', now()->startOfDay());
                            } elseif ($data['value'] === 'this_week') {
                                $query->where('failed_at', '>=', now()->startOfWeek());
                            } elseif ($data['value'] === 'last_week') {
                                $query->where('failed_at', '>=', now()->subWeek()->startOfWeek())
                                    ->where('failed_at', '<', now()->startOfWeek());
                            } elseif ($data['value'] === 'this_month') {
                                $query->where('failed_at', '>=', now()->startOfMonth());
                            } elseif ($data['value'] === 'last_month') {
                                $query->where('failed_at', '>=', now()->subMonth()->startOfMonth())
                                    ->where('failed_at', '<', now()->startOfMonth());
                            }
                        }
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
        return FilamentFailedJobsPlugin::get()->shouldRegisterNavigation();
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
