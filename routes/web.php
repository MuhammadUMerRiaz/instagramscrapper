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

Route::get('/', function () {
return view('welcome');
})->name('s.a');

Route::post('/insta', 'InstaStartController@index')->name('insta.start');

Route::get('/insta', 'InstaStartController@index2')->name('insta.start');

