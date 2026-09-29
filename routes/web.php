<?php
use App\Http\Controllers\AccueilController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\JoueurController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/testController', action:[AccueilController::class, 'index']);

Route::get('/la-page/{page}', [AccueilController::class, 'page'])->name (name:'page');

//Route::get('{a}/{b}/{operation}', [AccueilController::class, 'calculer'])->name('calculer');

Route::resource('cours', ClasseController::class);

Route::resource('joueurs', JoueurController::class);



// Page accessible par tout le monde
Route::resource('absences', AbsenceController::class);
//

/*Page accessible seulement par les connectés, sinon renvoyé vers login
Route::middleware('auth')->group(function (){
    Route::resource('absences', AbsenceController::class);
});
*/

