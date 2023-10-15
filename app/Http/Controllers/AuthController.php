<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            return new UserResource(Auth::user());
        }

        return response()->json([
            'message' => trans('validation.invalid'),
            'errors' => [
                'user' => [trans('auth.failed')],
            ],
        ], 422);
    }

    public function register(StoreUserRequest $request)
    {
        $attributes = $request->validated();

        $attributes['password'] = Hash::make($attributes['password']);

        $user = User::create($attributes);
        Auth::login($user);

        event(new Registered($user));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
