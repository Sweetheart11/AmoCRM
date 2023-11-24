<?php

declare(strict_types = 1);

use App\Controllers\HomeController;
use Slim\App;
use App\Controllers\FormController;
use App\Controllers\CRMAuthController;
use App\Services\AmoCRMAuthService;

return function (App $app) {
    $app->get('/', [HomeController::class, 'index']);
    $app->get('/auth', [CRMAuthController::class, 'authCRM']);
    $app->post('/amo', [FormController::class, 'sendForm']);
};
