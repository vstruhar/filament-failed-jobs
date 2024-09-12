<?php

namespace Vstruhar\FilamentFailedJobs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    use HasFactory;

    protected $table = 'failed_jobs';
}
