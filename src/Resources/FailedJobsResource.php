<?php

namespace Vstruhar\FilamentFailedJobs\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Livewire\Component;
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
                TextInput::make('connection')
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
                    ->label('UUID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('exception')
                    ->formatStateUsing(fn (Model $record): string => (string) str($record->exception)->before(': ')->afterLast('\\')),
                TextColumn::make('message')
                    ->wrap()
                    ->state(fn (Model $record): string => (string) str($record->exception)->between(': ', '. ')),
                TextColumn::make('models')
                    ->state(fn (Model $record): string => collect($record->payload['tags'])->map(fn ($tag) => str($tag)->afterLast('\\'))->join('<br>'))
                    ->html(),
                TextColumn::make('connection')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('queue')
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('failed_at')
                    ->label('Failed at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('failed_at', 'desc')
            ->bulkActions([
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
                Action::make('retry_all')
                    ->button()
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (Component $livewire) {
                        Artisan::call('queue:retry', ['id' => 'all']);
                        $livewire->resetTable();
                    }),
            ])
            ->actions([
                Action::make('retry')
                    ->button()
                    ->icon('heroicon-m-arrow-path')
                    ->action(function (Model $record, Component $livewire) {
                        $record->retry();
                        $livewire->resetTable();
                    }),
            ])
            ->filters([
                SelectFilter::make('queue')
                    ->options(
                        fn () => FailedJob::groupBy('queue')
                            ->get()
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
                        fn () => FailedJob::groupBy('connection')
                            ->get()
                            ->pluck('connection')
                            ->mapWithKeys(fn ($value) => [$value => Str::title($value)])
                    )
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            return $query->where('connection', $data['value']);
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
