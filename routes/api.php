<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WebsiteController;
use App\Http\Controllers\API\AuthenticationController;


Route::controller(AuthenticationController::class)->group(function() {
    Route::post('login', 'login')->name('login');
    Route::post('logout',  'logout')->name('logout')->middleware('auth:sanctum');
    Route::post('logout-all-devices',  'logoutAllDevices')->name('logout.all-devices')->middleware('auth:sanctum');
});



/**
 * Guest User's Routes
 */
    Route::apiResource('websites', WebsiteController::class)
    ->only(['index', 'show'])
    ->missing(function (Request $request) {
        return response()->json([
            'status' =>'Failed',
            'message'=>'Website not found'
        ], 404);
    });

// End guest routes 


/**
 *  Auth User's Routes
 */
Route::middleware([
    'auth:sanctum',
])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::apiResource('websites', WebsiteController::class)
    ->except(['index', 'show'])
    ->missing(function (Request $request) {
        return response()->json([
            'status' =>'Failed',
            'message'=>'Website not found'
        ], 404);
    });

    Route::controller(WebsiteController::class)->group( function (){
        Route::post('/websites/{website}/vote', 'vote')
        ->name('vote')
        ->missing(function (Request $request) {
            return response()->json([
                'status' =>'Failed',
                'message'=>'Website not found'
            ], 404);
        });

        Route::delete('/websites/{website}/unvote', 'unvote')
        ->name('unvote')
        ->missing(function (Request $request) {
            return response()->json([
                'status' =>'Failed',
                'message'=>'Website not found'
            ], 404);
        });

    })->name('websites.');
});

