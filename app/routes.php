<?php
// Routes

$app->get('/', 'App\Controller\FrontController:homeAction')
    ->setName('homepage');

$app->get('/admin', 'App\Controller\BackController:dashboardAction')
    ->setName('dashboard');
