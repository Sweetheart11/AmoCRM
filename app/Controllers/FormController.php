<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\DataObjects\UserProfileDTO;
use App\Contracts\CRMAuthServiceInterface;
use App\Contracts\CRMCurlServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FormController
{
    public function __construct(
        private readonly CRMAuthServiceInterface $authService,
        private readonly CRMCurlServiceInterface $curlService,
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

        $access_token = $this->authService->refreshAccess();

        $body = $this->curlService->sendLead($userProfile, $access_token);

        $response->getBody()->write($body);

        return $response;
    }

}
