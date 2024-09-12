<?php

namespace Vstruhar\FilamentFailedJobs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vstruhar\FilamentFailedJobs\FilamentFailedJobs
 */
class FilamentFailedJobs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vstruhar\FilamentFailedJobs\FilamentFailedJobs::class;
    }
}
