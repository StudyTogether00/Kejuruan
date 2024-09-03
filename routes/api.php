<?php

use App\Http\Controllers\Jurusancontroller;
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

Route::post("/test", [Jurusancontroller::class,"index"]);

Route::post("/List", [Jurusancontroller::class,"Lists"]);