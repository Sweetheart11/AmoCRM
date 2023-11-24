<?php

declare(strict_types = 1);

namespace App\DataObjects;

class UserProfileDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $tel,
        public int $price
    ) {
    }
}