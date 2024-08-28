<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;


//regiser
Route::post('register', [ApiController::class, 'register']);

//login
Route::post('login', [ApiController::class, 'login']);

//logout
Route::post('/logout', [ApiController::class, 'logout'])->middleware('auth:sanctum');


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
