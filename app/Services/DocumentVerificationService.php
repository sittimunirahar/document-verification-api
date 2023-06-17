<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\DocumentVerificationRequest;
use App\Models\DocumentVerificationResult;
use App\Services\DocumentValidatorService;
use Illuminate\Support\Facades\Auth;

class DocumentVerificationService
{
  const ERROR_STORE_RESULT = 'An error occurred while storing the result.';

  public function __construct(protected DocumentValidatorService $validatorService)
  {
  }

  public function verify(DocumentVerificationRequest $request): array
  {
    $requestData = $this->transformToJson($request);

    $result = $this->validatorService->validate($requestData);

    $issuerName = $requestData['data']['issuer']['name'];
    $verificationResult = [
      'issuer' => $issuerName,
      'result' => $result,
    ];

    try {
      $this->storeResult($result);
    } catch (\Exception $e) {
      $verificationResult['error'] = self::ERROR_STORE_RESULT;
    }

    return $verificationResult;
  }

  public function transformToJson(DocumentVerificationRequest $request): array
  {
    $file = $request->file('file');
    $fileContent = file_get_contents($file->path());
    $jsonContent = json_decode($fileContent, true);

    $transformedData = $this->formatData($jsonContent);
    return $transformedData;
  }

  public function formatData(?array $jsonContent): array
  {
    return [
      'data' => [
        'id' => $jsonContent['data']['id'] ?? '',
        'name' => $jsonContent['data']['name'] ?? '',
        'recipient' => [
          'name' => $jsonContent['data']['recipient']['name'] ?? '',
          'email' => $jsonContent['data']['recipient']['email'] ?? ''
        ],
        'issuer' => [
          'name' => $jsonContent['data']['issuer']['name'] ?? '',
          'identityProof' => [
            'type' => $jsonContent['data']['issuer']['identityProof']['type'] ?? '',
            'key' => $jsonContent['data']['issuer']['identityProof']['key'] ?? '',
            'location' => $jsonContent['data']['issuer']['identityProof']['location'] ?? ''
          ]
        ],
        'issued' => $jsonContent['data']['issued'] ?? ''
      ],
      'signature' => [
        'type' => $jsonContent['signature']['type'] ?? '',
        'targetHash' => $jsonContent['signature']['targetHash'] ?? ''
      ]
    ];
  }

  public function storeResult(string $result): void
  {
    $verificationResult = new DocumentVerificationResult();
    $verificationResult->user_id = Auth::id();
    $verificationResult->file_type = DocumentVerificationRequest::SUPPORTED_FILE_FORMAT;
    $verificationResult->result = $result;
    $verificationResult->save();
  }
}
