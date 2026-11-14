<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class SubscribeNewsletterController
{
    /**
     * Bedrock does not choose an email service provider. Replace this response with a call to your provider's API.
     */
    public function __invoke(SubscribeNewsletterRequest $request): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'message' => 'The newsletter is not configured. Connect an email service provider in SubscribeNewsletterController.',
            ],
            Response::HTTP_SERVICE_UNAVAILABLE
        );
    }
}
