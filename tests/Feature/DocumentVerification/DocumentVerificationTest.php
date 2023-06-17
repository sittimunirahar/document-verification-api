<?php

include_once 'tests/TestConstants.php';

use App\Models\User;
use App\Services\DocumentValidatorService;
use App\Http\Requests\DocumentVerificationRequest;
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

test('authorized user is able to verify document', function () {
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

test('authorized user is able to verify document with incomplete data', function () {
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

test('authorized user is not allowed to upload file bigger than 2MB', function () {
  $file = UploadedFile::fake()->image('document.json', 3000, 3000)->size(DocumentVerificationRequest::MAX_FILE_SIZE + 1);

  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $file,
  ]);

  $response->assertStatus(HTTP_POST_DOCUMENT_NOT_SUPPORTED)
    ->assertJson(EXPECTED_INVALID_FILE_SIZE_MESSAGE);
});

test('authorized user is only allowed to upload file in json', function () {
  $file = UploadedFile::fake()->createWithContent('document.pdf', 'hello');

  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $file,
  ]);

  $response->assertStatus(HTTP_POST_DOCUMENT_NOT_SUPPORTED)
    ->assertJson(EXPECTED_INVALID_FILE_TYPE_MESSAGE);
});

test('unauthorized user is not allowed to verify document', function () {
  $localFile = $this->file;

  $this->refreshApplication();
  $this->assertGuest();

  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $localFile,
  ]);

  $response->assertStatus(HTTP_POST_UNAUTHORIZED);
});

test('stores verification result in database', function () {
  $response = $this->postJson(DOCUMENT_VERIFICATION_API_URL, [
    'file' => $this->file,
  ]);

  $response
    ->assertStatus(HTTP_POST_SUCCESS);
  $this->assertDatabaseHas('verification_results', [
    'user_id' => $this->userId,
    'file_type' => DocumentVerificationRequest::SUPPORTED_FILE_FORMAT,
    'result' => DocumentValidatorService::VERIFIED,
  ]);
});
