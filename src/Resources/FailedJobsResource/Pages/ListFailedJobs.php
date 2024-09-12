<?php

namespace Vstruhar\FilamentFailedJobs\Resources\FailedJobsResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Vstruhar\FilamentFailedJobs\Resources\FailedJobsResource;

class ListFailedJobs extends ListRecords
{
    public static string $resource = FailedJobsResource::class;

    public function getActions(): array
    {
        return [];
    }

    public function getHeaderWidgets(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Failed jobs';
    }
}
