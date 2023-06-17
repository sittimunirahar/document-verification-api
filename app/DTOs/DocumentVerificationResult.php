<?php

namespace App\DTOs;

class DocumentVerificationResult
{
    public function __construct(public string $issuer, public string $result)
    {
    }
}
