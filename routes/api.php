<?php

use App\Http\Controllers\BE\MstData\JurusanController;
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
});
