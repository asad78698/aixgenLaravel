<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class Sanctum extends Controller
{
    public function register(Request $request)
    {

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'User Registered Successfully!',
            'data' => $user
        ]);

    }


    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user) {

            if (
                Auth::attempt([
                    'email' => $request->email,

                    'password' => $request->password

                ])
            ) {

                $token = $user->createToken('token-name')->plainTextToken;

                return response()->json([
                    'message' => 'User Logged In Successfully!',
                    'token' => $token
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Invalid Credentials!'
            ]);
        }
    }

    public function dashboard()
    {
        return response()->json([
            'message' => 'Welcome to the Dashboard!'
        ]);
    }

}
