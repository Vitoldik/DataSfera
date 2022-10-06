<?php

namespace App\Providers;

use App\Actions\GetLeadActionAction;
use App\Actions\InitAmoCRMIntegrationActionAction;
use App\Contracts\GetLeadActionContract;
use App\Contracts\InitAmoCRMIntegrationActionContract;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider {

    public array $bindings = [
        InitAmoCRMIntegrationActionContract::class => InitAmoCRMIntegrationActionAction::class,
        GetLeadActionContract::class => GetLeadActionAction::class
    ];

}
