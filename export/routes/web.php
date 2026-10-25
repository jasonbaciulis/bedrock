<?php

declare(strict_types=1);

use App\Http\Controllers\SubscribeNewsletterController;
use Illuminate\Support\Facades\Route;

// Route for handling newsletter subscriptions.
Route::post('/newsletter', SubscribeNewsletterController::class)
    ->name('newsletter');
