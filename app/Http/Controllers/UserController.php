<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Models\Code;

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
        if (Code::where('phone', $request->phone)->get()[0]) {
            $code = Code::where('phone', $request->phone)->get()[0]->code;
        }

        if ($request->code === $code) {
            User::where('id', Auth::User()->id)->update([
                'phone'=> $request->phone
            ]);

            return response('', 200);
        }

        return response('', 422);
    }
}
