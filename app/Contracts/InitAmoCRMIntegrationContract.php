<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface InitAmoCRMIntegrationContract {

    public function __invoke(Request $request);

}
