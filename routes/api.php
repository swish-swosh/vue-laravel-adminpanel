<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\LogController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ResourceController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\ResourceTypeController;
use App\Http\Controllers\Api\V1\ResourceAccessController;
use App\Http\Controllers\Api\V1\ResourceTypeGroupController;

use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\Auth\RefreshTokenController;
use App\Http\Controllers\Api\V1\Auth\RegisterUserController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\EmailPasswordRecoveryController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationNotificationController;

// GUEST Middleware - none logged in users only
Route::middleware(['guest'])->group(function () {

    // register new user, verification invitation follows
    Route::post('/V1/auth/register', [RegisterUserController::class, '__invoke']);

    // get user credentials, user must be verified to be able to log in
    Route::post('/V1/auth/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login');

    // OAUTH2 uses refresh token to get new accessToken (when expired)
    Route::post('/V1/auth/refreshToken', [RefreshTokenController::class, '__invoke']);

    // user forgets password, sends form with email address to: (must be verified to be able)
    Route::post('/V1/auth/forgot-password', [EmailPasswordRecoveryController::class, '__invoke'])
        ->name('password.email');

    // user creates new password, sends form credentials to:
    Route::post('/V1/auth/reset-password', [PasswordResetController::class, '__invoke'])
        ->middleware(['throttle:6,1'])
        ->name('password.reset');

    // RESEND email notification when verification confirmation email has expired
    Route::post('/V1/auth/resend-notification', [EmailVerificationNotificationController::class, '__invoke'])   // #5  
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Verify email to be called after user registration,
    // or when expired, when a resend is done
    Route::get('/V1/auth/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');
});


// Routes demanding to be authenticated
Route::middleware(['auth:api'])->group(function () {

    // retrieve user for posted bearer token
    Route::post('/V1/retrieveUser', [UserController::class, 'retrieveUser']);

    Route::post('/V1/auth/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::post('/V1/fileUploads', [UploadController::class, 'store']);
    Route::post('/V1/fileDeletes', [UploadController::class, 'delete']);

    // COMPONENTS
    Route::get('/V1/countries', [CountryController::class, 'index']);
    Route::get('/V1/countries/{id}', [CountryController::class, 'show']);
});

// auth handled by extra policy added to the corresponding resource controllers
Route::delete('/V1/destroy-many-resources', [ResourceController::class, 'destroyMany']);
Route::delete('/V1/logs-destroy-many', [LogController::class, 'destroyMany']);

// resource controllers. resources use policies for access control.
Route::resources([
    '/V1/roles' => RoleController::class,
    '/V1/logs' => LogController::class,
    '/V1/resources' => ResourceController::class,
    '/V1/resourceTypes' => ResourceTypeController::class,
    '/V1/resourceTypeGroups' => ResourceTypeGroupController::class,
    '/V1/resourceAccesses' => ResourceAccessController::class
]);

// graceful json page not found return route alternative sample, \\Exception\Handler does this
Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If the error persists, please contact info@swish-swosh.com'], 404);
});
