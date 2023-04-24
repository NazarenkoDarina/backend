<?php

namespace App\Http\Controllers;

use App\Services\SmsAeroService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function sendCode(Request $request)
    {
        $smsAero = new SmsAeroService();
    }
}
