<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Link;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/links', function (Request $request) {
    return $request->user()
        ->links()                     
        ->select('id', 'alias', 'url', 'click_count', 'created_at')
        ->latest()
        ->get();
});

Route::post('/shorten', function (Request $request) {
    $data = $request->validate([
        'url' => ['required', 'url'],
        'alias' => ['nullable', 'alpha_dash:ascii', 'min:3', 'max:30', 'unique:links,alias'],
    ]);

    // Generate random alias if one is not provided
    $alias = $data['alias'] ?? Str::random(6);

    // Ensure uniqueness
    while (Link::where('alias', $alias)->exists()) {
        $alias = Str::random(6);
    }

    $link = Link::create([
        'url' => $data['url'],
        'alias' => $alias,
        'user_id' => optional($request->user())->id ?? null
    ]);

    return response()->json([
        'short_url' => url($alias),
        'alias'     => $alias,
        'url'       => $link->url
    ]);
});