<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionShareController;
use App\Http\Controllers\ProviderController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/provider', [ProviderController::class, 'showProviders'])
    ->name('provider.index');

Route::get('/provider/{providerId}/genre', [ProviderController::class, 'showGenres'])
     ->name('provider.genres');

Route::get('/provider/{providerId}/genre/{genreId}/createSession', [ProviderController::class, 'showGenerateSession'])
     ->name('provider.generateSession');

Route::get('/provider/{providerId}/{genre}/movies', [ProviderController::class, 'movies'])
    ->name('provider.movies');

Route::match(['get','post'], '/provider/{providerId}/genre/{genreId}/createSession/code', [SessionShareController::class, 'create']) 
    ->name('provider.sessionCode');

Route::post('/session/{code}/join', [SessionShareController::class, 'join'])
     ->name('session.join');
    
Route::get ('/session/join-session', function () {
    return view('session.join');
});

Route::get('/session/{code}', [SessionShareController::class, 'showSession'])
     ->name('session.show');

Route::post('/session/{code}/update-approval', [SessionShareController::class, 'updateApproval'])
    ->name('session.approve');

Route::get('/session/{code}/participants', [SessionShareController::class, 'participantsJson']);

Route::get('/api/session-id', function(Request $req) {
  return response()->json(['id' => $req->session()->getId()]);
});