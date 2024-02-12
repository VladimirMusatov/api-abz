<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

Route::get('token', [ApiController::class , 'get_token']);
Route::get('users/{id}', [ApiController::class, 'get_user_by_id']);
Route::get('/users', [ApiController::class, 'get_users']);
Route::post('users', [ApiController::class , 'create_user']);
Route::get('positions', [ApiController::class, 'positions']);
