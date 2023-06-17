<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use App\DTOs\DocumentValidationResult;

class DocumentValidatorService
{

  const INVALID_RECIPIENT = 'invalid_recipient';
  const INVALID_ISSUER = 'invalid_issuer';
  const INVALID_SIGNATURE = 'invalid_signature';
  const VERIFIED = 'verified';

  const DNS_RR_TYPE = 16;
  const DNS_URL = "https://dns.google/resolve";
  const DNS_TYPE = 'TXT';

  const HASHING_ALGO = 'sha256';

  public function validate(array $verificationResource): string
  {
    $recipientValidationResult = $this->validateRecipient($verificationResource);
    $issuerValidationResult = $this->validateIssuer($verificationResource);
    $signatureValidationResult = $this->validateSignature($verificationResource);

    $result = match (true) {
      !$recipientValidationResult->isValid() => $recipientValidationResult->getErrorMessage(),
      !$issuerValidationResult->isValid() =>  $issuerValidationResult->getErrorMessage(),
      !$signatureValidationResult->isValid() => $signatureValidationResult->getErrorMessage(),
      default => self::VERIFIED,
    };

    return $result;
  }

  public function validateRecipient(array $verificationResource): DocumentValidationResult
  {
    $recipient = $verificationResource['data']['recipient'] ?? '';

    $isValid = is_array($recipient) && ($recipient['name'] && $recipient['email']);
    $errorMessage = !$isValid ? self::INVALID_RECIPIENT : '';

    return new DocumentValidationResult($isValid, $errorMessage);
  }

  public function validateIssuer(array $verificationResource): DocumentValidationResult
  {
    $issuer = $verificationResource['data']['issuer'] ?? '';

    $issuerName = $issuer['name'] ?? null;
    $identityProof = $issuer['identityProof'] ?? null;

    $issuerValid = $issuerName && $identityProof;
    $foundKey = false;

    if (!$issuerValid) {
      return new DocumentValidationResult($issuerValid, self::INVALID_ISSUER);
    } else {
      $key = $identityProof['key'];
      $location = $identityProof['location'];

      $foundKey = $this->dnsLookup($key, $location);
    }

    $errorMessage = !$foundKey ? self::INVALID_ISSUER : '';
    return new DocumentValidationResult($foundKey, $errorMessage);
  }

  private function dnsLookup(string $key, string $location): bool
  {
    $foundKey = false;
    $client = new Client();

    // This block defines the success and failure callbacks for the DNS lookup promise.
    $promise = $client->getAsync(self::DNS_URL, [
      'query' => [
        'name' => $location,
        'type' => self::DNS_TYPE,
      ],
    ]);

    // Checks DNS records for provided key and returns validation result
    $promise->then(
      function ($response) use ($key, &$foundKey) {
        $dnsRecords = json_decode($response->getBody()->getContents(), true) ?? '';

        if ($dnsRecords && isset($dnsRecords['Answer'])) {
          foreach ($dnsRecords['Answer'] as $record) {
            if ($record['type'] == self::DNS_RR_TYPE && strpos($record['data'], $key) !== false) {
              $foundKey = true;
              break;
            }
          }
        }
      },
      function () {
        return new DocumentValidationResult(false, self::INVALID_ISSUER);
      }
    )->wait();

    return $foundKey;
  }

  public function validateSignature(array $verificationResource): DocumentValidationResult
  {
    $targetHash = $verificationResource['signature']['targetHash'] ?? '';

    $propertyPaths = $this->buildPropertyPath($verificationResource['data']);
    $hashedPropertyPaths = $this->hashPropertyPath($propertyPaths);
    $mergedHashedPropertyPaths = json_encode($hashedPropertyPaths);
    $computedTargetHash = hash(self::HASHING_ALGO, $mergedHashedPropertyPaths);

    $isValid = $targetHash === $computedTargetHash;
    $errorMessage = !$isValid ? self::INVALID_SIGNATURE : '';
    return new DocumentValidationResult($isValid, $errorMessage);
  }

  // Flattens a nested array into a new array with keys as property paths
  private function buildPropertyPath(array $data, string $prefix = ''): array
  {
    $results = [];

    foreach ($data as $key => $value) {
      $newKey = $prefix === '' ? $key : "{$prefix}.{$key}";

      if (is_array($value)) {
        $results = array_merge($results, $this->buildPropertyPath($value, $newKey));
      } else {
        $results[$newKey] = $value;
      }
    }

    return $results;
  }

  // Sort all the generated hashes alphabetically and hash them all together using specified hashing algorithm. 
  // This will provide the target hash of the file.
  private function hashPropertyPath(array $propertyPaths): array
  {
    $hashList = [];

    foreach ($propertyPaths as $key => $value) {
      $jsonPair = json_encode([$key => $value]);
      array_push($hashList, hash(self::HASHING_ALGO, $jsonPair));
    }

    sort($hashList, SORT_STRING);
    return $hashList;
  }
}
