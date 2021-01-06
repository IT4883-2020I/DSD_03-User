<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/image-incident', 'Service\IncidentService@saveImageIncident');
Route::post('/video-incident', 'Service\IncidentService@saveVideoIncident');
Route::post('/upload-file', 'Service\IncidentService@uploadFile');

Route::post('/login', ['uses' => 'Service\UserService@login']);
Route::post('/register', ['uses' => 'Service\UserService@register']);
Route::post('/change-password', ['uses' => 'Service\UserService@changePassword']);
Route::post('/forgot-password', ['uses' => 'Service\UserService@forgotPassword']);
Route::get('/verify-token', ['uses' => 'Service\UserService@verifyToken']);
Route::post('/send-email-forgot-password', ['uses' => 'Service\UserService@sendEmailForgotPassword']);
Route::patch("/user/update-status", ['uses' => 'Service\UserService@updateStatusActivation']);

Route::get('/generate-department', ['uses' => 'Service\UserService@generateDepartment']);

Route::get('/generate-permission', ['uses' => 'Service\UserService@generatePermission']);

Route::get('/get-permission', ['uses' => 'Service\UserService@getPermission']);

Route::post('/get-user-name', ['uses' => 'Service\UserService@getUsersName']);