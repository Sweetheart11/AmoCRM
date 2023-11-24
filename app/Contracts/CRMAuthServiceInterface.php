<?php

declare(strict_types=1);

namespace App\Contracts;

interface CRMAuthServiceInterface
{
    public function refreshAccess(): string;

    public function authRequest(): string;
}

