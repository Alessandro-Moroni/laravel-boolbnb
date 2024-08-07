<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HouseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TomTomController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SponsorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/autocomplete', [TomTomController::class, 'autocomplete'])->name('autocomplete');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [HouseController::class, 'index'])->name('home');

        // rotte crud messages
        Route::resource('messages', MessageController::class);



        // rotte crud houses
        Route::resource('houses', HouseController::class);

        //rotta statistiche

        Route::get('/stats/{house}', [HouseController::class,'stats'])->name('stats');


        // rotte custom per mettere in ordine

        Route::get('orderby/{direction}/{column}', [HouseController::class, 'orderBy'])->name('orderby');

        // rotte custom per pagina castelli cancellati
        Route::get('deleted-castles', [HouseController::class, 'deleted'])->name('deleted');

        // rotte custom per recuperare castello
        Route::put('retrieve-castles/{id}', [HouseController::class, 'retrieve'])->name('retrieve');

        // rotte custom per sponsor
        Route::get('/sponsors/{house}', [SponsorController::class, 'sponsors'])->name('sponsors');
        Route::get('/sponsors/{sponsor}/{house}', [SponsorController::class, 'createSponsor'])->name('create-sponsors');

        // rotte custom per pagamento
        Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::post('payment/create', [PaymentController::class, 'create'])->name('payment.create');
        Route::get('payment/token', [PaymentController::class, 'generateClientToken'])->name('payment.token');


    });

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

require __DIR__ . '/auth.php';
