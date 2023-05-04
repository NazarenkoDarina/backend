<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function AddNameUser(Request $request)
    {
        User::where('id', Auth::User()->id)->update([
            'name_customer'=> $request->name
        ]);
    }
    public function ChangePhoneUser(Request $request)
    {
        User::where('id', Auth::User()->id)->update([
            'phone'=> $request->phone
        ]);
    }
}
