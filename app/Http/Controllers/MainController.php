<?php

namespace App\Http\Controllers;

use App\Contracts\InitAmoCRMIntegrationActionContract;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request, InitAmoCRMIntegrationActionContract $action) {
        return $action($request);
    }
}
