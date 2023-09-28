<?php
use App\Http\Controllers\PresentationController;

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
});
//ppt merger
Route::get('/home', [PresentationController::class, 'index']);
Route::post('/upload', [PresentationController::class, 'upload']);
Route::get('/download', [PresentationController::class, 'download']);


//pdf merger
Route::any('/merge-pdf', 'PdfMergeController@mergePdf');
Route::get('/pdf_merger', 'PdfMergeController@index');
