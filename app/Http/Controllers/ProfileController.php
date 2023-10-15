<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show(string $username)
    {
        $profile = User::whereUsername($username)
            ->firstOrFail();

        return new ProfileResource($profile);
    }

    public function follow(Request $request, string $username)
    {
        $profile = User::whereUsername($username)
            ->firstOrFail();

        $user = $request->user();
        $user->followers()
            ->syncWithoutDetaching($profile);

        return new ProfileResource($profile);
    }

    public function unfollow(User $user)
    {
        $User = auth()->user();

        $User->followers()->detach($user);

        return new ProfileResource($user);
    }
}
