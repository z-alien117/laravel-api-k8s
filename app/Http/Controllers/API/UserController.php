<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Validator;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    public function index(): JsonResponse
    {
        $users = User::all();
    
        return $this->sendResponse(UserResource::collection($users), 'users retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return $this->sendResponse(new UserResource($user), 'User created successfully.');
    }

    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('user not found.');
        }

        return $this->sendResponse(new UserResource($user), 'user retrieved successfully.');
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = bcrypt($input['password']);
        $user->save();

        return $this->sendResponse(new UserResource($user), 'user updated successfully.');
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }

}