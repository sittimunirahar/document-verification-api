<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DocumentVerificationService;
use App\Http\Requests\DocumentVerificationRequest;
use Illuminate\Http\JsonResponse;

class DocumentVerificationController extends Controller
{

    public function __construct(protected DocumentVerificationService $verificationService)
    {
    }

    public function __invoke(DocumentVerificationRequest $request): JsonResponse
    {
        $result = $this->verificationService->verify($request);

        return new JsonResponse($result);
    }
}
