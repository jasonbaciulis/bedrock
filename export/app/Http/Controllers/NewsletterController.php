<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(NewsletterRequest $request)
    {
        // Your newsletter logic here

        return response()->json(
            [
                'success' => true,
            ],
            Response::HTTP_OK
        );
    }
}
