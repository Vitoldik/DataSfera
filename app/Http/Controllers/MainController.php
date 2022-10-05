<?php

namespace App\Http\Controllers;

use App\Actions\InitAmoCRMIntegration;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request, InitAmoCRMIntegration $action) {
        return $action($request);
    }
}
