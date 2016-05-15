<?php

// DIC configuration
$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Flash Messages
$container['flash'] = function ($c) {
    return new Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// PDO
$container['pdo'] = function ($c) {
    $settings = $c->get('settings');
    try {
        $db = new \PDO(
            $settings['pdo']['driver'].':dbname='.$settings['pdo']['database'].';host='.$settings['pdo']['host'],
            $settings['pdo']['user'],
            $settings['pdo']['passwd']
        );
    } catch (\PDOException $e) {
        $errMsg = 'DB conection failed : ' . $e->getMessage();
        $c->get('logger')->error($errMsg);
        throw new \Exception($errMsg);
    }
    return $db;
};