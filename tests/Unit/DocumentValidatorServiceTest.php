<?php

include_once 'tests/TestConstants.php';

use App\Services\DocumentValidatorService;
use App\Services\DocumentVerificationService;

beforeEach(function () {
    $this->validator = new DocumentValidatorService();
    $this->service = new DocumentVerificationService($this->validator);
});

test('document validation returns verified', function () {
    $result = $this->validator->validate(FORMATTED_DOCUMENT_DATA_COMPLETE);

    expect($result)
        ->toBe(DocumentValidatorService::VERIFIED);
});

test('document validation returns invalid_recipient', function () {
    $result = $this->validator->validate(DOCUMENT_DATA_WITHOUT_RECIPIENT);

    expect($result)
        ->toBe(DocumentValidatorService::INVALID_RECIPIENT);

    $validateRecipientResult = $this->validator->validateRecipient(DOCUMENT_DATA_WITHOUT_RECIPIENT);

    $this->assertEquals($validateRecipientResult, EXPECTED_VALIDATION_RESULT_INVALID_RECIPIENT);
});

test('document validation returns invalid_issuer', function () {
    $result = $this->validator->validate(DOCUMENT_DATA_WITHOUT_ISSUER);

    expect($result)
        ->toBe(DocumentValidatorService::INVALID_ISSUER);

    $validateIssuerResult = $this->validator->validateIssuer(DOCUMENT_DATA_WITHOUT_ISSUER);

    $this->assertEquals($validateIssuerResult, EXPECTED_VALIDATION_RESULT_INVALID_ISSUER);
});

test('document validation returns invalid_signature', function () {
    $result = $this->validator->validate(DOCUMENT_DATA_WITHOUT_SIGNATURE);

    expect($result)
        ->toBe(DocumentValidatorService::INVALID_SIGNATURE);

    $validateSignatureResult = $this->validator->validateSignature(DOCUMENT_DATA_WITHOUT_SIGNATURE);

    $this->assertEquals($validateSignatureResult, EXPECTED_VALIDATION_RESULT_INVALID_SIGNATURE);
});
