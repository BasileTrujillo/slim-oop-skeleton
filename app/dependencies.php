<?php

// DIC configuration
$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

/**
 * Twig
 *
 * @param \Slim\Container $c
 *
 * @return \Slim\Views\Twig
 */
$container['view'] = function (\Slim\Container $c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

/**
 * Slim Flash Message
 *
 * @param \Slim\Container $c
 *
 * @return \Slim\Flash\Messages
 */
$container['flash'] = function (\Slim\Container $c) {
    return new Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

if ($container->get('settings')['debugbar']['enabled'] === true) {
    /**
     * Debug Bar
     *
     * @param \Slim\Container $c
     *
     * @return \DebugBar\StandardDebugBar
     */
    $container['debugbar'] = function (\Slim\Container $c) {
        $debugbar = new DebugBar\StandardDebugBar();

        // Add settings array to Config Collector
        if ($c->get('settings')['debugbar']['collectors']['config'] === true) {
            $debugbar->addCollector(new DebugBar\DataCollector\ConfigCollector($c->get('settings')->all()));
        }

        return $debugbar;
    };
}

/**
 * Monolog
 *
 * @param \Slim\Container $c
 *
 * @return \Monolog\Logger
 */
$container['logger'] = function (\Slim\Container $c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));

    // Add Monolog instance to Debug Bar Data Collector
    if ($settings['debugbar']['enabled'] === true && $settings['debugbar']['collectors']['monolog'] === true) {
        $c->get('debugbar')->addCollector(
            new DebugBar\Bridge\MonologCollector($logger)
        );
    }

    return $logger;
};

/**
 * Load PDO and attach it to Debug Bar (if enabled)
 *
 * @param \Slim\Container $c
 *
 * @return PDO
 * @throws Exception
 */
$container['pdo'] = function (\Slim\Container $c) {
    $settings = $c->get('settings');
    try {
        $db = new \PDO(
            $settings['pdo']['driver'].':dbname='.$settings['pdo']['database'].';host='.$settings['pdo']['host'],
            $settings['pdo']['user'],
            $settings['pdo']['passwd']
        );

        // Add PDO instance to Debug Bar Data Collector
        if ($settings['debugbar']['enabled'] === true && $settings['debugbar']['collectors']['pdo'] === true) {
            $c->get('debugbar')->addCollector(
                new DebugBar\DataCollector\PDO\PDOCollector(
                    new DebugBar\DataCollector\PDO\TraceablePDO($db)
                )
            );
        }
    } catch (\PDOException $e) {
        $errMsg = 'DB conection failed : ' . $e->getMessage();
        $c->get('logger')->error($errMsg);
        throw new \Exception($errMsg);
    }
    return $db;
};