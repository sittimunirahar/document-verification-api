<?php

include_once 'tests/TestConstants.php';

use App\DTOs\DocumentVerificationResult as DocumentVerificationResultDTO;
use App\Http\Requests\DocumentVerificationRequest;
use App\Models\User;
use App\Services\DocumentValidatorService;
use App\Services\DocumentVerificationService;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
  $this->validator = new DocumentValidatorService();
  $this->service = new DocumentVerificationService($this->validator);

  $fileContent = json_encode(FORMATTED_DOCUMENT_DATA_COMPLETE);
  $this->file = UploadedFile::fake()->createWithContent('document.json', $fileContent);

  $this->request = new DocumentVerificationRequest();
  $this->request->files->set('file', $this->file);
});

test('transforms file content to JSON', function () {
  $result = $this->service->transformToJson($this->request);

  expect($result)
    ->toBe($this->service->formatData($result));
});

test('verifies document and returns expected result', function () {
  $result = $this->service->verify($this->request);

  $this->assertInstanceOf(DocumentVerificationResultDTO::class, $result);
  $this->assertEquals(EXPECTED_RESULT_VERIFIED['issuer'], $result->issuer);
  $this->assertEquals(EXPECTED_RESULT_VERIFIED['result'], $result->result);
});

test('formats verification data', function () {
  $incompleteData = DOCUMENT_DATA_WITHOUT_ISSUER;
  $this->assertArrayNotHasKey('issuer', $incompleteData['data']);

  $formattedData = $this->service->formatData($incompleteData);
  $this->assertIsArray($formattedData);
  $this->assertArrayHasKey('issuer', $formattedData['data']);
});

test('stores verification results', function () {
  $user = User::factory()->create();
  $this->actingAs($user);
  $testResult = DocumentValidatorService::VERIFIED;

  $this->service->storeResult($testResult);

  $this->assertDatabaseHas('verification_results', [
    'user_id' => $user->id,
    'file_type' => DocumentVerificationRequest::SUPPORTED_FILE_FORMAT,
    'result' => $testResult,
  ]);
});
