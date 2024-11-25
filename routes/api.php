<?php

use App\Http\Controllers\BE\MstData\JurusanController;
use App\Http\Controllers\BE\MstData\MapelController;
use App\Http\Controllers\BE\MstData\SiswaController;
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

Route::prefix('MasterData')->group(function () {
    Route::prefix('Jurusan')->group(function () {
        Route::post('List', [JurusanController::class, "Lists"]);
        Route::post('Save', [JurusanController::class, "Save"]);
        Route::post('Delete', [JurusanController::class, "Delete"]);
    });
    Route::prefix('Siswa')->group(function () {
        Route::post('List', [SiswaController::class, "Lists"]);
        Route::post('Save', [SiswaController::class, "Save"]);
        Route::post('Delete', [SiswaController::class, "Delete"]);
    });
    Route::prefix('Mapel')->group(function () {
        Route::post('List', [MapelController::class, "Lists"]);
        Route::post('Save', [MapelController::class, "Save"]);
        Route::post('Delete', [MapelController::class, "Delete"]);
    });
});
