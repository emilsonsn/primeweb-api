<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OccurrenceController;
use App\Http\Controllers\PhoneCallController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;

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

Route::post('login', [AuthController::class, 'login']);

Route::get('validateToken', [AuthController::class, 'validateToken']);
Route::post('recoverPassword', [UserController::class, 'passwordRecovery']);
Route::post('updatePassword', [UserController::class, 'updatePassword']);


Route::get('validateToken', [AuthController::class, 'validateToken']);

Route::middleware('jwt')->group(function(){

    Route::middleware(AdminMiddleware::class)->group(function() {
        // Middleware do admin
    });

    Route::prefix('dashboard')->group(function() {
        Route::get('cards', [DashboardController::class, 'cards']);
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('user')->group(function(){
        Route::get('all', [UserController::class, 'all']);
        Route::get('search', [UserController::class, 'search']);
        Route::get('me', [UserController::class, 'getUser']);
        Route::post('create', [UserController::class, 'create']);
        Route::patch('{id}', [UserController::class, 'update']);
        Route::post('block/{id}', [UserController::class, 'userBlock']);
        Route::delete('{id}', [UserController::class, 'delete']);
    });

    // Rotas para Contacts
    Route::prefix('contact')->group(function(){
        Route::get('all', [ContactController::class, 'all']);
        Route::get('search', [ContactController::class, 'search']);
        Route::post('create', [ContactController::class, 'create']);
        Route::patch('{id}', [ContactController::class, 'update']);
        Route::delete('{id}', [ContactController::class, 'delete']);
    });

    // Rotas para Segments
    Route::prefix('segment')->group(function(){
        Route::get('all', [SegmentController::class, 'all']);
        Route::get('search', [SegmentController::class, 'search']);
        Route::post('create', [SegmentController::class, 'create']);
        Route::patch('{id}', [SegmentController::class, 'update']);
        Route::delete('{id}', [SegmentController::class, 'delete']);
    });

    // Rotas para Phone Calls
    Route::prefix('phone-call')->group(function(){
        Route::get('all', [PhoneCallController::class, 'all']);
        Route::get('search', [PhoneCallController::class, 'search']);
        Route::post('create', [PhoneCallController::class, 'create']);
        Route::patch('{id}', [PhoneCallController::class, 'update']);
        Route::delete('{id}', [PhoneCallController::class, 'delete']);
    });  
    
    Route::prefix('occurrence')->group(function(){
        Route::get('all', [OccurrenceController::class, 'all']);
        Route::get('search', [OccurrenceController::class, 'search']);
        Route::post('create', [OccurrenceController::class, 'create']);
        Route::post('resend-email/{id}', [OccurrenceController::class, 'resendEmail']);        
        Route::patch('{id}', [OccurrenceController::class, 'update']);
        Route::delete('{id}', [OccurrenceController::class, 'delete']);
    }); 
    
    Route::prefix('log')->group(function(){
        Route::get('search', [LogController::class, 'search']);
    }); 

    Route::prefix('notification')->group(function(){
        Route::get('search', [NotificationController::class, 'search']);
        Route::post('see', [NotificationController::class, 'see']);
    }); 
});