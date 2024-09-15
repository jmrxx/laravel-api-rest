<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Function show all users
     */
    public function index() {

        $users = User::all();

        if($users->isEmpty()) {
         $response = [
            'message' => 'No users found',
            'status' => 200
         ];

         return response()->json($response, 404);
        }

        return response()->json($users, 200);
    }

    /**
     * show the users for the id
     */
    public function show($id) {

        $user = User::find($id);

        if(!$user) {
            $response = [
                'message' => 'User not found',
                'status' => 404
            ];
            return response()->json($response, 404);
        }

        return response()->json($user, 200);

    }

    /***
     * save the user in database
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'perfil_picture' => 'nullable|string',
            'biography' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string',
        ]);

        if($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($response, 400);
        }

        // saved user into database
        $user = User::create([
            'username' => $request->username,
            'lastname' => $request->lastname,
            'perfil_picture' => $request->perfil_picture, 
            'biography' => $request->biography, 
            'status' => $request->status, 
            'email' => $request->email, 
            'password' => $request->password,
        ]);

        if(!$user) {
            $response = [
                '' => 'Failed to save user',
                'status' => 404
            ];
            return response()->json($response, 404);
        }

        return response()->json($user, 200);
    }


    /**
     * update the user for the id
     */
    public function update(Request $request, $id) {

        $user = User::find($id);

        if(!$user) {
            $response = [
                'message' => 'user not found',
                'status' => 404
            ];

            return response()->json($response, 404);
        }

        

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'perfil_picture' => 'nullable|string',
            'biography' => 'nullable|string|max:1000',
            'status' => 'nullable|in:active,inactive',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string',
        ]);

        if($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 404
            ];

            return response()->json($response, 404);
        }

        $user->username = $request->username;
        $user->lastname = $request->lastname;
        $user->perfil_picture = $request->perfil_picture;
        $user->biography = $request->biography;
        $user->status = $request->status;
        $user->email = $request->email;
        $user->password = $request->password;

        $user->save();

        return response()->json($user, 200);

    }

    /**
     * Delete user for the id
     */
    public function delete($id) {
        $user = User::find($id);

        if(!$user) {
            $response = [
                'Message' => 'User not found',
                'status' => 404,
            ];

            return response()->json($response, 404);
        }

        $user->delete();

        $response = [
            'message' => 'Deleted user',
            'status' => 200
        ];

        return response()->json($response, 200);
    }

    /**
     * Update partial using method http patch
     */
    
     public function updatePartial(Request $request, $id) {
        $user = User::find($id);

        if(!$user) {
            $response = [
                'message' => 'User not found',
                'status' => 404  
            ];

            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'string|max:255',
            'lastname' => 'string|max:255',
            'perfil_picture' => 'string',
            'biography' => 'string|max:1000',
            'status' => 'in:active,inactive',
            'email' => '|email|max:255|unique:users,email',
            'password' => 'string',
        ]);

        if($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($response, 400);
        }

        if($request->has('username'))$user->username = $request->username;
        if($request->has('lastname'))$user->lastname = $request->lastname;
        if($request->has('perfil_picture'))$user->perfil_picture = $request->perfil_picture;
        if($request->has('biography'))$user->biography = $request->biography;
        if($request->has('status'))$user->status = $request->status;
        if($request->has('email'))$user->email = $request->email;
        if($request->has('password'))$user->password = $request->password;

        $user->save();

        return response()->json($user, 200);
     }
}