<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc,dns'],
        ]);

        try {
            // Your newsletter logic here
        } catch (Exception $e) {
            return response()->json(
                [
                    'errors' => [
                        'email' => [$e->getMessage()],
                    ],
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
