<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Link;

Route::get('/home', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/', function () {
    return Inertia::render('URLShortener');
})->name('shortener');

Route::get('/{alias}', function (string $alias) {
    $link = Link::where('alias', $alias)->firstOrFail();

    // increment click count
    $link->increment('click_count');

    // redirect to original URL
    return redirect()->away($link->url);
})->where('alias', '[A-Za-z0-9-_]+');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
