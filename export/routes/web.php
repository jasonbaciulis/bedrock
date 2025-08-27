<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsletterController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

// Route for handling newsletter subscriptions.
Route::post('/newsletter', NewsletterController::class)
    ->middleware([HandlePrecognitiveRequests::class])
    ->name('newsletter');

// The Sitemap route to the sitemap.xml
Route::statamic('/sitemap.xml', 'sitemap/sitemap', [
    'layout' => null,
    'content_type' => 'application/xml',
]);
