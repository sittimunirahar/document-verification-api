<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DocumentVerificationService;
use Illuminate\Http\JsonResponse;

class DocumentVerificationController extends Controller
{
    public const SUPPORTED_FORMAT = 'json';
    public const MAX_FILE_SIZE = 2048;

    public function __construct(protected DocumentVerificationService $verificationService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {

        $request->validate([
            'file' => ['required', 'file', 'mimes:' . self::SUPPORTED_FORMAT, 'filled', 'max:' . self::MAX_FILE_SIZE],
        ]);

        $result = $this->verificationService->verify($request);

        return new JsonResponse($result);
    }
}
