<?php

define('HTTP_POST_SUCCESS', 200);
define('HTTP_POST_VALIDATION_FAILED', 422);
define('HTTP_POST_UNAUTHORIZED', 401);
define('FORMATTED_DOCUMENT_DATA_COMPLETE', [
  'data' => [
    'id' => '63c79bd9303530645d1cca00',
    'name' => 'Certificate of Completion',
    'recipient' => [
      'name' => 'Marty McFly',
      'email' => 'marty.mcfly@gmail.com'
    ],
    'issuer' => [
      'name' => 'Accredify',
      'identityProof' => [
        'type' => 'DNS-DID',
        'key' => 'did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller',
        'location' => 'ropstore.accredify.io'
      ]
    ],
    'issued' => '2022-12-23T00:00:00+08:00'
  ],
  'signature' => [
    'type' => 'SHA3MerkleProof',
    'targetHash' => '288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e'
  ],
]);
define('FORMATTED_DOCUMENT_DATA_INCOMPLETE', [
  'data' => [
    'id' => '63c79bd9303530645d1cca00',
    'name' => 'Certificate of Completion',
    'recipient' => [
      'name' => 'Marty McFly',
      'email' => 'marty.mcfly@gmail.com'
    ],
  ],
  'signature' => [
    'type' => 'SHA3MerkleProof',
    'targetHash' => '288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e'
  ],
]);
