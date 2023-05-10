<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\User;
use App\Services\SmsAeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function sendCode(Request $request)
    {
        $smsAero = new SmsAeroService();

        $rnd = mt_rand(1000, 9999);

        Code::create([
            'phone' => $request->phone,
            'code'  => $rnd
        ]);

        return $smsAero->send([$request->phone], 'Code: ' . $rnd);
    }

    public function register(Request $request)
    {
        if (Code::where('phone', $request->phone)->get()[0]) {
            $code = Code::where('phone', $request->phone)->get()[0]->code;
        }

        if ($request->code === $code) {
            User::create([
                'phone' => $request->phone
            ]);

            Auth::login(User::where('phone', $request->phone)->get()[0]);

            $user  = Auth::user();
            $token = Auth::user()->createToken('main')->plainTextToken;

            return [$user, $token];
        }

        return response('Введите верный код', 422);
    }

    public function Login(Request $request)
    {
        if (Code::where('phone', $request->phone)->get()[0]) {
            $code = Code::where('phone', $request->phone)->get()[0]->code;
        }

        if ($request->code === $code) {
            Auth::login(User::where('phone', $request->phone)->get()[0]);

            return Auth::user()->createToken('main')->plainTextToken;
        }

        return response('Введите верный код', 422);
    }
}
