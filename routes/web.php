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
    return view('pages/dashboard');
});

Route::get('/pages/Datajurusan', function () {
    return view( 'pages/Datajurusan');
});
Route::get('/pages/Datasiswa', function () {
    return view( 'pages/Datasiswa');
});
Route::get('/pages/Datamatapelajaran', function () {
    return view( 'pages/Datamatapelajaran');
});
Route::get('/pages/Databobot', function () {
    return view( 'pages/Databobot');
});
Route::get('/pages/Datanilaimatapelajaran', function () {
    return view( 'pages/Datanilaimatapelajaran');
});
Route::get('/pages/Normalisasi', function () {
    return view( 'pages/Normalisasi');
});
Route::get('/pages/Laporan', function () {
    return view( 'pages/Laporan');
});
