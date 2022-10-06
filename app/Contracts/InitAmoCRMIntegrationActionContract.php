<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface InitAmoCRMIntegrationActionContract {

    public function __invoke(Request $request);

}
