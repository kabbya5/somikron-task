<?php

use App\Http\Controllers\PDFGenaratorController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::controller(PDFGenaratorController::class)->group(function(){
    Route::get('/', 'index');
    Route::get('/generate/pdf', 'pdfGenerate');
});
