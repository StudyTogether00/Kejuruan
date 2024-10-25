<?php

use App\Http\Controllers\FE\MasterDataController;
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

Route::prefix("MasterData")->group(function () {
    Route::get("Jurusan", [MasterDataController::class, "Jurusan"]);
    Route::get("Siswa", [MasterDataController::class, "Siswa"]);
    Route::get("MataPelajaran", [MasterDataController::class, "MataPelajaran"]);
    Route::get("Bobot", [MasterDataController::class, "Bobot"]);
});

Route::get('/pages/Normalisasi', function () {
    return view('pages/Normalisasi');
});
Route::get('/pages/Laporan', function () {
    return view('pages/Laporan');
});
Route::get('/MasterData/MstUser', function () {
    return view('pages.MasterUser');
});
