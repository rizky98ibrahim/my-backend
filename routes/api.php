<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('roles', RoleController::class);
Route::apiResource('permissions', PermissionController::class);
Route::apiResource('users', UserController::class);

// ! Prefix /api/auth
Route::prefix('auth')->group(function () {
    // * Login
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // * Register
    Route::post('register', [AuthController::class, 'register'])->name('register');

    // * Verify Email
    Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email');

    // * Resend Verification Email
    Route::post('resend', [AuthController::class, 'resendPin'])->name('resend');

    // * Forgot Password
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');

    // * Verify Pin
    Route::post('verify-pin', [AuthController::class, 'verifyPin'])->name('verify-pin');

    // * Reset Password
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

    // * Logout & Get User
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'getUser'])->name('user');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
