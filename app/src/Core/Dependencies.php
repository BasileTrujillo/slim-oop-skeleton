<?php
namespace App\Core;

use DebugBar\Bridge\MonologCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\StandardDebugBar;
use League\CLImate\CLImate;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Slim\App;
use Slim\Container;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extension_Debug;

/**
 * Class Dependencies
 * Load and setup dependencies into Slim DI Container from provided App object
 *
 * @package App\Core
 */
class Dependencies
{
    /**
     * @var Container Slim DI Container
     */
    protected $dic;

    /**
     * Dependencies constructor.
     *
     * @param App $app Slim App Instance
     */
    public function __construct(App $app)
    {
        $this->dic = $app->getContainer();
    }

    /**
     * Auto load all dependencies
     * Call every public methods begining with "load" (such as "loadTwig")
     *
     * Add your onwn dependencies by creating methods begining with "load"
     */
    public function autoLoadDependencies()
    {
        $modelReflector = new \ReflectionClass(__CLASS__);
        $methods = $modelReflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($methods as $method) {
            if (strrpos($method->name, 'load', -strlen($method->name)) !== false) {
                $this->{$method->name}();
            }
        }
    }

    /**
     * Load Twig Slim view
     */
    public function loadTwig()
    {
        /**
         * Twig
         *
         * @param Container $c
         *
         * @return Twig
         */
        $this->dic['view'] = function (Container $c) {
            $settings = $c->get('settings');
            $view = new Twig($settings['view']['template_path'], $settings['view']['twig']);

            // Add extensions
            $view->addExtension(new TwigExtension($c->get('router'), $c->get('request')->getUri()));
            $view->addExtension(new Twig_Extension_Debug());
            $view->addExtension(new TwigAppExtension($c));

            return $view;
        };
    }

    /**
     * Load Slim Flash Message
     */
    public function loadFlash()
    {
        /**
         * Slim Flash Message
         *
         * @param Container $c
         *
         * @return Messages
         */
        $this->dic['flash'] = function (Container $c) {
            return new Messages();
        };
    }

    /**
     * Load Debug Bar Service if enabled
     */
    public function loadDebugBar()
    {
        if ($this->dic->get('settings')['debugbar']['enabled'] === true) {
            /**
             * Debug Bar
             *
             * @param Container $c
             *
             * @return StandardDebugBar
             */
            $this->dic['debugbar'] = function (Container $c) {
                $debugbar = new StandardDebugBar();

                // Add settings array to Config Collector
                if ($c->get('settings')['debugbar']['collectors']['config'] === true) {
                    $debugbar->addCollector(new ConfigCollector($c->get('settings')->all()));
                }

                return $debugbar;
            };
        }
    }

    /**
     * Load Monolog Service
     */
    public function loadMonolog()
    {
        /**
         * Monolog
         *
         * @param \Slim\Container $c
         *
         * @return Logger
         */
        $this->dic['logger'] = function (Container $c) {
            $settings = $c->get('settings');
            $logger = new Logger($settings['logger']['name']);
            $logger->pushProcessor(new UidProcessor());
            if (PHP_SAPI === 'cli') {
                $filename = $settings['logger']['filename_cli'];
            } else {
                $filename = $settings['logger']['filename'];
            }
            $logger->pushHandler(new StreamHandler($settings['logger']['path'].$filename, Logger::DEBUG));

            // Add Monolog instance to Debug Bar Data Collector
            if ($settings['debugbar']['enabled'] === true && $settings['debugbar']['collectors']['monolog'] === true) {
                $c->get('debugbar')->addCollector(
                    new MonologCollector($logger)
                );
            }

            return $logger;
        };
    }

    /**
     * Load PDO and attach it to Debug Bar (if enabled)
     */
    public function loadPDO()
    {
        /**
         * Load PDO
         *
         * @param Container $c
         *
         * @return \PDO
         * @throws \Exception
         */
        $this->dic['pdo'] = function (Container $c) {
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
                        new PDOCollector(
                            new TraceablePDO($db)
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
    }

    /**
     * Load League\CLImate Library
     */
    public function loadCLImate()
    {
        /**
         * Load CLImate
         *
         * @param Container $c
         *
         * @return CLImate
         */
        $this->dic['climate'] = function (Container $c) {
            return new CLImate();
        };
    }
}