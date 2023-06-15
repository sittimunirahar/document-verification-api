<?php

include_once __DIR__ . '/TestConstants.php';

use App\Http\Controllers\DocumentVerificationController;
use App\Models\User;
use App\Services\DocumentVerificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->actingAs($this->user);
  $this->userId = $this->user->id;
  Storage::fake('public');

  $jsonString = json_encode(FORMATTED_DOCUMENT_DATA_COMPLETE);
  $this->file = UploadedFile::fake()->createWithContent('document.json', $jsonString);
});

test('authenticated user successfully verify document', function () {
  $response = $this->postJson('/api/document_verification', [
    'file' => $this->file,
  ]);

  $response
    ->assertJson(
      fn (AssertableJson $json) =>
      $json
        ->where('result', DocumentVerificationService::VERIFIED)
        ->etc()
    )
    ->assertStatus(HTTP_POST_SUCCESS);
});

test('authenticated user successfully verify document with incomplete data', function () {
  $jsonStringIncomplete = json_encode(FORMATTED_DOCUMENT_DATA_INCOMPLETE);
  $this->file = UploadedFile::fake()->createWithContent('document.json', $jsonStringIncomplete);

  $response = $this->postJson('/api/document_verification', [
    'file' => $this->file,
  ]);

  $response
    ->assertJson(
      fn (AssertableJson $json) =>
      $json
        ->whereNot('result', DocumentVerificationService::VERIFIED)
        ->etc()
    )
    ->assertStatus(HTTP_POST_SUCCESS);
});

test('unauthenticated user cannot verify any document', function () {
  $localFile = $this->file;

  $this->refreshApplication();
  $this->assertGuest();

  $response = $this->postJson('/api/document_verification', [
    'file' => $localFile,
  ]);

  $response->assertStatus(HTTP_POST_UNAUTHORIZED);
});

test('authenticated user only can submit file bigger than 2MB', function () {
  $file = UploadedFile::fake()->image('document.json', 3000, 3000)->size(DocumentVerificationController::MAX_FILE_SIZE + 1);
  $response = $this->postJson('/api/document_verification', [
    'file' => $file,
  ]);

  $expectedResponse = [
    'message' => 'The file field must not be greater than 2048 kilobytes.',
    'errors' => [
      'file' => [
        'The file field must not be greater than 2048 kilobytes.'
      ]
    ]
  ];

  $response->assertStatus(HTTP_POST_VALIDATION_FAILED)
    ->assertJson($expectedResponse);
});

test('authenticated user only can submit file in json', function () {
  $file = UploadedFile::fake()->createWithContent('document.pdf', 'hello');
  $response = $this->postJson('/api/document_verification', [
    'file' => $file,
  ]);

  $expectedResponse = [
    'message' => 'The file field must be a file of type: json.',
    'errors' => [
      'file' => [
        'The file field must be a file of type: json.'
      ]
    ]
  ];

  $response->assertStatus(HTTP_POST_VALIDATION_FAILED)
    ->assertJson($expectedResponse);
});

test('verification result stored in database', function () {
  $response = $this->postJson('/api/document_verification', [
    'file' => $this->file,
  ]);

  $response
    ->assertStatus(HTTP_POST_SUCCESS);

  $this->assertDatabaseHas('verification_results', [
    'user_id' => $this->userId,
    'file_type' => DocumentVerificationController::SUPPORTED_FORMAT,
    'result' => DocumentVerificationService::VERIFIED,
  ]);
});
