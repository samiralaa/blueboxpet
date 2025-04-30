<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-all-cache', function () {
    $exitCodeCacheClear = Artisan::call('cache:clear');
    $exitCodeOptimize = Artisan::call('optimize');
    $exitCodeRouteCache = Artisan::call('route:cache');
    $exitCodeRouteClear = Artisan::call('route:clear');
    $exitCodeViewClear = Artisan::call('view:clear');
    $exitCodeConfigCache = Artisan::call('config:cache');
    $exitCodeStorageLink = Artisan::call('storage:link');
     // $exitCodeDbSeed = Artisan::call('db:seed'); // Added database seeding

    return response()->json([
        'message' => 'All caches cleared, storage linked, and system optimized.',
        'cache_clear' => $exitCodeCacheClear,
        'optimize' => $exitCodeOptimize,
        'route_cache' => $exitCodeRouteCache,
        'route_clear' => $exitCodeRouteClear,
        'view_clear' => $exitCodeViewClear,
        'config_cache' => $exitCodeConfigCache,

    ]);
});
