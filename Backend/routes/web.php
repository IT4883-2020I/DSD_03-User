<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PagesController@index')->name('home');

Route::get("/user", "HomeController@user")->name('user');
Route::get("/user-schedule", "HomeController@userSchedule")->name('user::schedule');
Route::get("/streaming", "HomeController@streaming")->name('streaming');
Route::get("/drone", "HomeController@drone")->name('drone');
Route::get("/drone-schedule", "HomeController@droneSchedule")->name('drone::schedule');
Route::get("/incident", "HomeController@incident")->name('incident');
Route::get("/image", "HomeController@image")->name('image');
Route::get("/video", "HomeController@video")->name('video');
Route::get("/area", "HomeController@area")->name('area');
Route::get("/path", "HomeController@path")->name('path');

Route::get('/statistic', 'HomeController@statistic')->name('statistic');

// Demo routes
Route::get('/datatables', 'PagesController@datatables');
Route::get('/ktdatatables', 'PagesController@ktDatatables');
Route::get('/select2', 'PagesController@select2');
Route::get('/icons/custom-icons', 'PagesController@customIcons');
Route::get('/icons/flaticon', 'PagesController@flaticon');
Route::get('/icons/fontawesome', 'PagesController@fontawesome');
Route::get('/icons/lineawesome', 'PagesController@lineawesome');
Route::get('/icons/socicons', 'PagesController@socicons');
Route::get('/icons/svg', 'PagesController@svg');

// Quick search dummy route to display html elements in search dropdown (header search)
Route::get('/quick-search', 'PagesController@quickSearch')->name('quick-search');

Auth::routes();