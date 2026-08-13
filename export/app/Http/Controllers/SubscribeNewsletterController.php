<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class SubscribeNewsletterController
{
    public function __invoke(SubscribeNewsletterRequest $request): JsonResponse
    {
        return response()->json(
            [
                'success' => true,
            ],
            Response::HTTP_OK
        );
    }
}
