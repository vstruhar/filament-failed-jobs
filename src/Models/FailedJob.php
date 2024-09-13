<?php

namespace Vstruhar\FilamentFailedJobs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class FailedJob extends Model
{
    use HasFactory;

    protected $table = 'failed_jobs';

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'payload' => 'array',
        'failed_at' => 'datetime',
    ];

    public function retry()
    {
        Artisan::call('queue:retry', ['id' => $this->uuid]);
    }

    public function exceptionItems()
    {
        return collect(explode("\n", $this->exception));
    }

    public function exceptionClass(): string
    {
        return (string) str($this->exceptionItems()->first())->before(': ')->afterLast('\\');
    }

    public function exceptionMessage(): string
    {
        return (string) str($this->exceptionItems()->first())->between(': ', '. ');
    }

    public function getModels(): Collection
    {
        $command = $this->payload['data']['command'] ?? null;

        return collect($command ? unserialize($command) : [])
            ->map(
                fn ($value) => ($value instanceof Model)
                    ? class_basename(get_class($value)) . ':' . $value->id
                    : null
            )
            ->filter();
    }
}
