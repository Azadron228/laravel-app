<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request)
    {
        if (empty($attrs = $request->validated())) {
            return response()->json([
                'message' => trans('validation.invalid'),
                'errors' => [
                    'any' => [trans('validation.required_at_least_one')],
                ],
            ], 422);
        }

        /** @var \app\models\user $user */
        $user = $request->user();

        $user->update($attrs);

        return new userresource($user);
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $avatarPath = Storage::disk('public')->put('avatars', $request->file('avatar'));
        $user = $request->user();
        $validatedData['avatar'] = $avatarPath;

        $user->update($validatedData);

        return new UserResource($user);
    }

}
