<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends Controller
{
    public function __invoke(NewsletterRequest $request): JsonResponse
    {
        return response()->json(
            [
                'success' => true,
            ],
            Response::HTTP_OK
        );
    }
}
