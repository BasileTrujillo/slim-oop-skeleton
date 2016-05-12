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

// Flash messages
$container['flash'] = function ($c) {
    return new Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
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
        $db = new PDO(
            $settings['pdo']['driver'].':dbname='.$settings['pdo']['database'].';host='.$settings['pdo']['host'],
            $settings['pdo']['user'],
            $settings['pdo']['passwd']
        );
    } catch (PDOException $e) {
        throw new \Exception('DB conection failed : ' . $e->getMessage());
    }
    return $db;
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container[App\Controller\FrontController::class] = function ($c) {
    return new App\Controller\FrontController($c->get('view'), $c->get('logger'), $c->get('settings'));
};

$container[App\Controller\BackController::class] = function ($c) {
    return new App\Controller\BackController($c->get('view'), $c->get('logger'), $c->get('settings'));
};