<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordResetToken;
use App\Http\Resources\PasswordResetTokenResource;

class PasswordResetTokenController extends Controller
{
    /**
     * Create a new token
     * 
     * Create a new password reset token and send it to the user
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request){
        $request->validate(['email' => 'required|string|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $passwordResetToken = PasswordResetToken::updateOrCreate(
            ['email' => $user->email],
            ['token' => Str::random(60)]
        );

        if($passwordResetToken) $user->notify(new PasswordResetRequest($passwordResetToken->token));
        else return response()->json(['message' => 'Failed to create password reset token'], 500);

        return response()->json(['message' => 'Password reset token created successfully', 'data' => $passwordResetToken], 200);    
    
    }

    /**
     * Find a token
     * 
     * Find a password reset token by its token
     * @unauthenticated
     * @param String $token
     * @return PasswordResetTokenResource
     */
    public function find(String $token ){
        $passwordResetToken = PasswordResetToken::where('token', $token)->first();
        
        if (!$passwordResetToken) return response()->json(['message' => 'Password reset token not found'], 404);
        
        if ($passwordResetToken->created_at->addMinutes(60)->isPast()) {
            $passwordResetToken->delete();
            return response()->json(['message' => 'Password reset token has expired'], 400);
        } 
    
        $user = User::where('email', $passwordResetToken->email)->first();
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        
        $user->notify(new PasswordResetSuccess());

        return new PasswordResetTokenResource($passwordResetToken);
    }

    /**
     * Reset a token
     * 
     * Reset a password using a password reset token
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function reset(Request $request){
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();
        
        $passwordResetToken = PasswordResetToken::where('email', $request->email)->firstOrFail();

        if (!$passwordResetToken) return response()->json(['message' => 'Password reset token not found'], 404);
    
       $user->password = hash::make($request->password);
        $user->save();

        $passwordResetToken->delete();
        $user->notify(new PasswordResetSuccess());

        return response()->json(['message' => 'Password reset success'], 200);    
    }
}
