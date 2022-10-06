<?php

namespace App\Http\Controllers\API;

use App\Contracts\GetLeadActionContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetLeadController extends Controller {
    public function index(Request $request, GetLeadActionContract $action) {
        return $action($request);
    }
}
