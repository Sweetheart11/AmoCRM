<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Services\AmoCRMAuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CRMAuthController 
{      
    public function __construct(
        private readonly AmoCRMAuthService $amoCRMAuthService
    ) {
    }

    public function authCRM(Request $request, Response $response) : Response
    {   
        $body = $this->amoCRMAuthService->authRequest();

        $response->getBody()->write($body);
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}