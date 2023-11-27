<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Contracts\CRMAuthServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CRMAuthController 
{      
    public function __construct(
        private readonly CRMAuthServiceInterface $authService
    ) {
    }

    public function authCRM(Request $request, Response $response) : Response
    {   
        $body = $this->authService->authRequest();

        $response->getBody()->write($body);
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}