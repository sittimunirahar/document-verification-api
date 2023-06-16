<?php

include_once 'tests/TestConstants.php';

use App\Models\User;
use App\Services\DocumentValidatorService;
use App\Http\Controllers\DocumentVerificationController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->actingAs($this->user);
  $this->userId = $this->user->id;
  Storage::fake('public');

  $fileContent = json_encode(FORMATTED_DOCUMENT_DATA_COMPLETE);
  $this->file = UploadedFile::fake()->createWithContent('document.json', $fileContent);
});

test('authorized user verifies document', function () {
  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $this->file,
  ]);

  $response
    ->assertJson(
      fn (AssertableJson $json) =>
      $json
        ->where('result', DocumentValidatorService::VERIFIED)
        ->etc()
    )
    ->assertStatus(HTTP_POST_SUCCESS);
});

test('authorized user verifies document with incomplete data', function () {
  $incompleteFileContent = json_encode(DOCUMENT_DATA_WITHOUT_ISSUER);
  $this->file = UploadedFile::fake()->createWithContent('document.json', $incompleteFileContent);

  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $this->file,
  ]);

  $response
    ->assertJson(
      fn (AssertableJson $json) =>
      $json
        ->whereNot('result', DocumentValidatorService::VERIFIED)
        ->etc()
    )
    ->assertStatus(HTTP_POST_SUCCESS);
});

test('unauthorized user not allowed to verify document', function () {
  $localFile = $this->file;

  $this->refreshApplication();
  $this->assertGuest();

  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $localFile,
  ]);

  $response->assertStatus(HTTP_POST_UNAUTHORIZED);
});

test('authenticated user only can submit file bigger than 2MB', function () {
  $file = UploadedFile::fake()->image('document.json', 3000, 3000)->size(DocumentVerificationController::MAX_FILE_SIZE + 1);
  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $file,
  ]);

  $response->assertStatus(HTTP_POST_DOCUMENT_NOT_SUPPORTED)
    ->assertJson(EXPECTED_INVALID_FILE_SIZE_MESSAGE);
});

test('authenticated user only can submit file in json', function () {
  $file = UploadedFile::fake()->createWithContent('document.pdf', 'hello');
  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $file,
  ]);

  $response->assertStatus(HTTP_POST_DOCUMENT_NOT_SUPPORTED)
    ->assertJson(EXPECTED_INVALID_FILE_TYPE_MESSAGE);
});

test('verification result stored in database', function () {
  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $this->file,
  ]);

  $response
    ->assertStatus(HTTP_POST_SUCCESS);

  $this->assertDatabaseHas('verification_results', [
    'user_id' => $this->userId,
    'file_type' => DocumentVerificationController::SUPPORTED_FORMAT,
    'result' => DocumentValidatorService::VERIFIED,
  ]);
});
