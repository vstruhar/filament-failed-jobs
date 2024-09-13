<?php

namespace Vstruhar\FilamentFailedJobs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
