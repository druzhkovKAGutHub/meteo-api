<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'errors' => ['Не правильный email/пароль']
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken($request->header('User-Agent'));
        $token = $tokenResult->token;
        /*if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);*/
        $token->save();

        $roles = $user->roles;
        $permissions = collect($user->permissions->toArray());
        foreach($roles as $role)
            $permissions = $permissions->merge($role->permissions->toArray());

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'permissions' => $permissions->pluck('slug')
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $user = $request->user();

        $roles = $user->roles;
        $permissions = collect($user->permissions->toArray());
        foreach($roles as $role)
            $permissions = $permissions->merge($role->permissions->toArray());

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'permissions' => $permissions->pluck('slug')
        ]);
    }
}
