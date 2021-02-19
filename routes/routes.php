<?php

/**
 * @var $this Router
 */

use Aigletter\Core\Components\Router\Router;
use App\Controllers\AuthController;
use App\Controllers\PageController;

$this->addRoute(Router::METHOD_GET, '/page/view', [new PageController(), 'viewAction']);

$this->get('/', function(){
    echo 'Main page';
});

$authController = new AuthController();

$this->get('/auth/form', [$authController, 'form']);

$this->post('/auth/login', [$authController, 'login']);

$this->get('/auth/view', [$authController, 'view']);

$this->get('/auth/update', [$authController, 'update']);

$this->post('/auth/store', [$authController, 'store']);

$this->get('/payment/send', [new \App\Controllers\PaymentController(), 'pay']);