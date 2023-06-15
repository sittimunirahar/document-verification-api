<?php

namespace App\DTOs;

class DocumentValidationResult
{
  public function __construct(private bool $isValid, private string $errorMessage = '')
  {
  }

  public function isValid(): bool
  {
    return $this->isValid;
  }

  public function getErrorMessage(): string
  {
    return $this->errorMessage;
  }
}
