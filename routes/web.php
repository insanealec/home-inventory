<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  $user = request()->user();
  if ($user) {
      return redirect('/dashboard');
  }
    return view('app');
});

// Forward all authenticated web routes to Vue router
Route::get('{any}', function (Request $request) {
    $user = $request->user();
    if (!$user) {
        return redirect('/');
    }
    return view('app', ['user' => $user]);
})->where('any', '.*');
