<?php

namespace App\Http\Controllers;

use App\Services\SmsAeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function sendCode(Request $request)
    {
        $smsAero = new SmsAeroService();

        //Auth::login(User::where('phone', '79125784822')->get()[0]);

        //return Auth::user()->createToken('main')->plainTextToken;
        //return $smsAero->send(['79923009730'], 'Все норм работает');

        return 'asd';
    }

    function login(Request $request)
    {
        if ( ! Auth::attempt(['phone' => $request->phone, 'password' => ''])) {
            $user  = Auth::user();
            $token = Auth::user()->createToken('main')->plainTextToken;

            return compact($user, $token);
        }

        $user  = Auth::user();
        $token = Auth::user()->createToken('main')->plainTextToken;

        return compact($user, $token);
    }
}
