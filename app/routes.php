<?php
// Routes

// Front-office route
$app->get('/', 'App\Controller\FrontController:homeAction')
    ->setName('homepage');

// Back-office route
$app->get('/admin', 'App\Controller\BackController:dashboardAction')
    ->setName('dashboard');
