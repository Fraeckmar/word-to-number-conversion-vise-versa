<?php

use App\Http\Controllers\ConvertionController;
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

Route::post('/convert', [ConvertionController::class, 'convert']);
Route::get('/convert', [ConvertionController::class, 'convert']);
