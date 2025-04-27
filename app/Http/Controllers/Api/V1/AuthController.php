<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller 
{

    /**
     * Register
     * 
     * Register a new user and return a token.
     * @unauthenticated
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function register(UserStoreRequest $request) {
        
        $user = User::create([
            'username' => $request->username,
            'lastname' => $request->lastname,
            'perfil_picture' => $request->perfil_picture || NULL, 
            'biography' => $request->biography || NULL,
            'email' => $request->email, 
            'password' => Hash::make($request->password),
        ]);

        $accessToken = $user->createToken('authToken')->plainTextToken;
        $user->assignRole('user');

        return response()->json(['user' => new UserResource($user), 'access_token' => $accessToken], 200);
    }

    /**
     * Login
     * 
     * Authenticate the user and return a token.
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse|UserResource
     */
    public function login(Request $request) {
        $credencials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if(!Auth::attempt($credencials)) {
            return response()->json(['message' => 'Invalid Credentials'], 500);
        }

        /** @var App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token
        ],200);

     }

     /**
      * Logout
      *
      * Logout the user by deleting the current access token.
      * @return noContent
      */
      public function logout()
      {
        /** @var \App\Models\User $authUser */
        
        $authUser = Auth::user();
        $authUser->user()->currentAccessToken()->delete();
        return response()->noContent();
      }

     /**
      * Profile
      *
      * Get the authenticated user's profile.
      * @return UserResource
      */
     public function profile() {
        return new UserResource(Auth::user());
     }
}

?>