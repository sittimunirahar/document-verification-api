<?php

use App\DTOs\DocumentValidationResult;
use App\Services\DocumentValidatorService;

define('DOCUMENT_VERIFICATION_API_URL', '/api/document_verification');
define('HTTP_POST_SUCCESS', 200);
define('HTTP_POST_DOCUMENT_NOT_SUPPORTED', 422);
define('HTTP_POST_UNAUTHORIZED', 401);

define('DOCUMENT_DATA', [
  'id' => '63c79bd9303530645d1cca00',
  'name' => 'Certificate of Completion',
]);

define('DOCUMENT_DATA_RECIPIENT', [
  'recipient' => [
    'name' => 'Marty McFly',
    'email' => 'marty.mcfly@gmail.com'
  ],
]);

define('DOCUMENT_DATA_ISSUER', [
  'issuer' => [
    'name' => 'Accredify',
    'identityProof' => [
      'type' => 'DNS-DID',
      'key' => 'did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller',
      'location' => 'ropstore.accredify.io'
    ]
  ],
  'issued' => '2022-12-23T00:00:00+08:00'
]);

define('DOCUMENT_DATA_SIGNATURE', [
  'signature' => [
    'type' => 'SHA3MerkleProof',
    'targetHash' => '288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e'
  ],
]);

define('FORMATTED_DOCUMENT_DATA_COMPLETE', [
  'data' => [
    ...DOCUMENT_DATA,
    ...DOCUMENT_DATA_RECIPIENT,
    ...DOCUMENT_DATA_ISSUER,
  ],
  ...DOCUMENT_DATA_SIGNATURE,
]);

define('DOCUMENT_DATA_WITHOUT_ISSUER', [
  'data' => [
    ...DOCUMENT_DATA,
    ...DOCUMENT_DATA_RECIPIENT,
  ],
  ...DOCUMENT_DATA_SIGNATURE,
]);

define('DOCUMENT_DATA_WITHOUT_RECIPIENT', [
  'data' => [
    ...DOCUMENT_DATA,
    ...DOCUMENT_DATA_ISSUER,
  ],
  ...DOCUMENT_DATA_SIGNATURE,
]);

define('DOCUMENT_DATA_WITHOUT_SIGNATURE', [
  'data' => [
    ...DOCUMENT_DATA,
    ...DOCUMENT_DATA_ISSUER,
    ...DOCUMENT_DATA_RECIPIENT,
  ],
]);

define('EXPECTED_INVALID_FILE_SIZE_MESSAGE', [
  'message' => 'The file field must not be greater than 2048 kilobytes.',
  'errors' => [
    'file' => [
      'The file field must not be greater than 2048 kilobytes.'
    ]
  ]
]);

define('EXPECTED_INVALID_FILE_TYPE_MESSAGE', [
  'message' => 'The file field must be a file of type: json.',
  'errors' => [
    'file' => [
      'The file field must be a file of type: json.'
    ]
  ]
]);

define('EXPECTED_RESULT_VERIFIED', [
  'issuer' => 'Accredify',
  'result' => DocumentValidatorService::VERIFIED,
]);

define('EXPECTED_VALIDATION_RESULT_INVALID_ISSUER', new DocumentValidationResult(false, DocumentValidatorService::INVALID_ISSUER));
define('EXPECTED_VALIDATION_RESULT_INVALID_RECIPIENT', new DocumentValidationResult(false, DocumentValidatorService::INVALID_RECIPIENT));
define('EXPECTED_VALIDATION_RESULT_INVALID_SIGNATURE', new DocumentValidationResult(false, DocumentValidatorService::INVALID_SIGNATURE));
