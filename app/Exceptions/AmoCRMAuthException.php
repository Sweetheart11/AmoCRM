<?php

declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

class AmoCRMAuthException extends RuntimeException
{
    private $errors = [
        301 => 'Moved permanently.',
        400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
        401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
        403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
        404 => 'Not found.',
        500 => 'Internal server error.',
        502 => 'Bad gateway.',
        503 => 'Service unavailable.'
    ];

    public function __construct(int $code = 400)
    {
        parent::__construct($this->errors[$code] ?? 'Undefined error');
    }
    
}