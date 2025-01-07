<?php

use App\Http\Controllers\FE\MasterDataController;
use App\Http\Controllers\FE\ProcessController;
use App\Http\Controllers\FE\ReportController;
use App\Http\Controllers\FE\RouteController;
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

Route::get("/", [RouteController::class, "Dashboard"]); //->middleware("websession");
Route::get("/Login", [RouteController::class, "Login"]);
Route::prefix("MasterData")->group(function () {
    Route::get("Jurusan", [MasterDataController::class, "Jurusan"]);
    Route::get("Siswa", [MasterDataController::class, "Siswa"]);
    Route::get("MataPelajaran", [MasterDataController::class, "MataPelajaran"]);
    Route::get("Bobot", [MasterDataController::class, "Bobot"]);
});
Route::prefix("Process")->group(function () {
    Route::get("Nilai", [ProcessController::class, "Nilai"]);
});
Route::prefix("Report")->group(function () {
    Route::get("Normalisasi", [ReportController::class, "Normalisasi"]);
});
