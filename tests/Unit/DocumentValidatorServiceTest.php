<?php

include_once 'tests/TestConstants.php';

use App\Services\DocumentValidatorService;
use App\Services\DocumentVerificationService;

beforeEach(function () {
    $this->validator = new DocumentValidatorService();
    $this->service = new DocumentVerificationService($this->validator);
});

test('validates document with complete data', function () {
    $result = $this->validator->validate(FORMATTED_DOCUMENT_DATA_COMPLETE);

    expect($result)
        ->toBe(DocumentValidatorService::VERIFIED);
});

test('validates recipient with missing data', function () {
    $result = $this->validator->validate(DOCUMENT_DATA_WITHOUT_RECIPIENT);
    $validateRecipientResult = $this->validator->validateRecipient(DOCUMENT_DATA_WITHOUT_RECIPIENT);

    expect($result)
        ->toBe(DocumentValidatorService::INVALID_RECIPIENT);
    $this->assertEquals($validateRecipientResult, EXPECTED_VALIDATION_RESULT_INVALID_RECIPIENT);
});

test('validates issuer with missing data', function () {
    $result = $this->validator->validate(DOCUMENT_DATA_WITHOUT_ISSUER);
    $validateIssuerResult = $this->validator->validateIssuer(DOCUMENT_DATA_WITHOUT_ISSUER);

    expect($result)
        ->toBe(DocumentValidatorService::INVALID_ISSUER);
    $this->assertEquals($validateIssuerResult, EXPECTED_VALIDATION_RESULT_INVALID_ISSUER);
});

test('validates signature with missing data', function () {
    $result = $this->validator->validate(DOCUMENT_DATA_WITHOUT_SIGNATURE);
    $validateSignatureResult = $this->validator->validateSignature(DOCUMENT_DATA_WITHOUT_SIGNATURE);

    expect($result)
        ->toBe(DocumentValidatorService::INVALID_SIGNATURE);
    $this->assertEquals($validateSignatureResult, EXPECTED_VALIDATION_RESULT_INVALID_SIGNATURE);
});
