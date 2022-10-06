<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface GetLeadActionContract {

    public function __invoke(Request $request);

}
