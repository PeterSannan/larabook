<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $validated_data = $request->validate([
            'name' => 'required|max:55',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);
       
        $validated_data['password'] = bcrypt($validated_data['password']);
        $user = User::create($validated_data);
        $token = $user->createToken('authToken')->accessToken;
        
        return response([
            'user' => $user,
            'token' => $token
        ]);
    }


    public function login(Request $request) {
        $login_data = $request->validate([
            'email' => 'required',
            'password' => 'required '
        ]);

        if (!Auth::attempt($login_data)){
            return response([
                'message'=>'Your credentials are incorrect'
            ]);
        }

        $access_token = auth()->user()->createToken('authToken')->accessToken;

        return response([
            'user'=>auth()->user(),
            'token'=>$access_token
        ]);
    }
}
