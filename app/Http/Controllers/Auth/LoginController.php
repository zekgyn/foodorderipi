<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function login(LoginRequest $request)
    {
    // Authenticated a user
    if (Auth::attempt($request->validated())) {
        $admin = auth()->user();

        // Token generated
        $token = $admin->createToken($admin->id)->plainTextToken;
        return response()->json(['id' => $admin->id, 'token' => $token,]);
    }

    return response()->json(['message' => ['Incorrect credentials']], 422);
 }
    public function logout(Request $request)
    {
        // Delete a current access token
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);

    }

    // /**
    //  * Where to redirect users after login.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = RouteServiceProvider::HOME;

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

}
