<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\DataObjects\UserProfileDTO;
use App\Services\AmoCRMAuthService;
use App\Services\AmoCRMCurlService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FormController
{
    public function __construct(
        private readonly AmoCRMAuthService $amoCRMAuthService,
        private readonly AmoCRMCurlService $curlService,
    )
    {
    }

    public function sendForm(Request $request, Response $response) : Response
    {
        $body = $request->getParsedBody();

        $userProfile = new UserProfileDTO(
            $body['name'] ?? "",
            $body['email'] ?? "",
            $body['tel'] ?? "",
            (int) $body['price'] ?? "",
        );

        $access_token = $this->amoCRMAuthService->refreshAccess();

        $body = $this->curlService->sendLead($userProfile, $access_token);

        $response->getBody()->write($body);

        return $response;
    }

}
