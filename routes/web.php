<?php
use App\Http\Controllers\AccueilController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\JoueurController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\UserManagementController;

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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.roles.index'))->name('index');

    Route::get('/roles', [RoleManagementController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleManagementController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}/abilities', [RoleManagementController::class, 'updateAbilities'])
        ->name('roles.abilities.update');
    Route::post('/roles/{role}/abilities', [RoleManagementController::class, 'storeAbility'])
        ->name('roles.abilities.store');
    Route::delete('/roles/{role}', [RoleManagementController::class, 'destroy'])->name('roles.destroy');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

/*Page accessible seulement par les connectés, sinon renvoyé vers login
Route::middleware('auth')->group(function (){
    Route::resource('absences', AbsenceController::class);
});
*/

