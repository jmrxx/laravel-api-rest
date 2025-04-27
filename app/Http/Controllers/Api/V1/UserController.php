<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     *  List all users
     * 
     * List all users in the database
     * @return JsonResponse|UserResource
     */
    public function index() {

        $users = User::all();

        if($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }

        return UserResource::collection($users);
    }

    /**
     * Show user by ID
     * 
     * Show a user by their ID
     * @param int $id
     * @return JsonResponse|UserResource
     */
    public function show(int $id) {

        $user = User::find($id);

        if(!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new  UserResource($user);
    }

    /**
     * Store a new user
     * 
     * Store a new user and return the created user
     * @param UserStoreRequest $request
     * @return JsonResponse|UserResource
     */
    public function store(UserStoreRequest $request) {

        $user = User::create([
            'username' => $request->username,
            'lastname' => $request->lastname,
            'perfil_picture' => $request->perfil_picture, 
            'biography' => $request->biography,
            'email' => $request->email, 
            'password' => Hash::make($request->password),
        ]);
        
        // Assign the role to the user
        $user->assignRole('user');
        
        return new UserResource($user);
    }


    /**
     * Update user
     * 
     * Update a user by their ID
     * @param int $id
     * @param UserUpdateRequest $request
     * @return JsonResponse|UserResource
     */
    public function update(UserUpdateRequest $request, int $id) {

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser || !$authUser->hasAnyRole(['admin', 'superadmin'])) {
            return response()->json(['message' => 'User does not have the right roles.'], 403);
        }
        $user = User::find($id);

        if(!$user) {
           return response()->json(['message' => 'user not found'], 404);
        }

        // Update the data except the password
        $user->fill($request->except(['password']))->save();


        if ($authUser && $authUser->hasAnyRole('admin', 'superadmin') && $request->filled('role')) {
            $user->syncRoles($request->role);
        }

        // If a new password is sent, it encrypts and updates it.
        if(request()->filled('password')){
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return new UserResource($user);
    }
    
    /**
     * Partially update user
     * 
     * Partially update a user using HTTP PATCH 
     * @param Request $request
     * @param int $id
     * @return JsonResponse|UserResource
     */
    
     public function updatePartial(Request $request, int $id) {

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser || !$authUser->hasAnyRole(['admin', 'superadmin'])) {
            return response()->json(['message' => 'User does not have the right roles.'], 500);
        }

        $user = User::find($id);

        if(!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate only the fields sent
        $validatedData = $request->validate([
            'username' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'perfil_picture' => 'sometimes|string',
            'biography' => 'sometimes|string|max:1000',
            'role' => 'sometimes|string|exists:roles,name',
            'email' => "sometimes|email|max:255|unique:users,email,{$id}",
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

         // Assign the validated data, except for the password
        $user->fill(collect($validatedData)->except(['password'])->toArray());
        

        if ($authUser && $authUser->hasAnyRole('admin', 'superadmin') && $request->filled('role')) {
            $user->syncRoles($request->role);
        }

        // If a new password is sent, it encrypts and updates it.
        if(!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        
        $user->save();

        return new UserResource($user);
    }

    /**
     * Delete user
     * 
     * Delete a user by their ID
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id) {
        $user = User::find($id);
    
        if(!$user) {
           return response()->json(['message' => 'User not found'], 404);
        }
    
        $user->delete();
    
        return response()->json(['message' => 'User deleted'], 200);
    }
}