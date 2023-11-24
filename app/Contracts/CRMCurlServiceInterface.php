<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DataObjects\UserProfileDTO;

interface CRMCurlServiceInterface
{
    public function CRMClient(string $access_token, array $data): array;

    public function CRMAuth(string $link, array $data): array;

    public function sendLead(UserProfileDTO $userProfile, string $access_token): string;
}
