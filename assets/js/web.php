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
    return view('login');
});


Route::resource('dashboard', 'App\Http\Controllers\Dashboardcontroller');

Route::resource('report', 'App\Http\Controllers\Reportcontroller');
Route::resource('apptour', 'App\Http\Controllers\Apptourcontroller');


Route::resource('signup', 'App\Http\Controllers\Signupcontroller');
Route::resource('chart', 'App\Http\Controllers\ChartController');


Route::resource('basecontent', 'App\Http\Controllers\BaseContentController');
Route::resource('site', 'App\Http\Controllers\Sitecontroller');

Route::resource('calender', 'App\Http\Controllers\Calandercontroller');
Route::resource('payments', 'App\Http\Controllers\Paymentcontroller');
Route::resource('qrhistory', 'App\Http\Controllers\Qrhistorycontroller');




