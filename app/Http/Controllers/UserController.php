<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('view', 'users');
        $users = User::orderBy('created_at', 'DESC')->paginate();

        return UserResource::collection($users);
    }

    public function show($id)
    {
        Gate::authorize('view', 'users');
        $user = User::find($id);
        return new UserResource($user);
    }

    public function store(UserCreateRequest $request)
    {
        Gate::authorize('edit', 'users');

        $user = User::create($request->only('first_name', 'last_name', 'email', 'role_id') + [
            'password' => Hash::make(12345)
        ]);
        return response(new UserResource($user), Response::HTTP_CREATED);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        Gate::authorize('edit', 'users');
        $user = User::find($id);
        $user->update($request->only('first_name', 'last_name', 'email', 'role_id'));


        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function destroy($id)
    {
        Gate::authorize('edit', 'users');
        User::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        $user = Auth::user();
        $user->update($request->only('first_name', 'last_name', 'email'));


        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }
}
