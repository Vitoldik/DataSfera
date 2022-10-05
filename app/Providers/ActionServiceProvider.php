<?php

namespace App\Providers;

use App\Actions\InitAmoCRMIntegration;
use App\Contracts\InitAmoCRMIntegrationContract;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider {

    public array $bindings = [
        InitAmoCRMIntegrationContract::class => InitAmoCRMIntegration::class,
    ];

}
