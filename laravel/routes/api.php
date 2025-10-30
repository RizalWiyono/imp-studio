<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Aws\Exception\AwsException;
use App\Http\Controllers\Api\ArticleApiController;
// ============================================
// 🔐 AUTHENTICATION ROUTES
// ============================================
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/signin', [AuthController::class, 'signin']);
    Route::middleware('auth:sanctum')->post('/signout', [AuthController::class, 'signout']);
});


Route::prefix('v1')->group(function () {
    Route::get('/articles', [ArticleApiController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleApiController::class, 'show']);
});

// ============================================
// 🧾 AUTH CHECK ROUTE
// ============================================
Route::middleware('auth:sanctum')->get('/auth/me', function (Request $request) {
    return response()->json([
        'status' => true,
        'user' => $request->user(),
    ]);
});
