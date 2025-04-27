<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PasswordResetTokenController;

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Password Reset
    Route::post('/password/create', [PasswordResetTokenController::class, 'create'])->middleware('throttle:5,1');
    Route::get('/password/find/{token}', [PasswordResetTokenController::class, 'find'])->middleware('throttle:5,1');
    Route::post('/password/reset', [PasswordResetTokenController::class, 'reset'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum', 'role_or_permission:admin|superadmin')->group(function() {

        Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:10,1');
        Route::post('/profile', [AuthController::class, 'profile'])->middleware('throttle:10,1');

        // Admin-only routes
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/update/{id}', [UserController::class, 'update']);
        Route::patch('/users/update/partial/{id}', [UserController::class, 'updatePartial']);
        Route::delete('/users/delete/{id}', [UserController::class, 'destroy']);
    });
});

Route::fallback(function () {
    return response()->json(['message' => 'Route not found.'], 404);
});
