<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('admin')->accessToken;
            $cookie = cookie('jwt', $token, 3600);

            return response([
                'token' => $token
            ]);
        }

        return response([
            'error' => 'Invalid Credential'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function register(RegisterRequest $request)
    {

        $user = User::create($request->only('first_name', 'last_name', 'email') + [
            'password' => Hash::make($request->password),
            'role_id' => 3
        ]);
        return response($user, Response::HTTP_CREATED);
    }

    public function logout()
    {
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'success'
        ]);
    }
}
