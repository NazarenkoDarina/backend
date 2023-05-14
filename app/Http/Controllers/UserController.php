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

    public function ComparisonOldPhone(Request $request){
        if($request->oldPhone==User::where('id', Auth::User()->id)->get()[0]->phone){
            return response('text',200);
        }
        else{
            return response('text',422);
        }
    }
    
    public function ChangePhoneUser(Request $request)
    {
        User::where('id', Auth::User()->id)->update([
            'phone'=> $request->phone
        ]);
    }
}
