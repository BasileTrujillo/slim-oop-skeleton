<?php
namespace App\Core;

use Slim\App;

/**
 * Class Routes
 * Register Slim routes into provided App object
 *
 * @package App\Core
 */
class Routes
{
    /**
     * @var App Slim App instance
     */
    private $app;

    /**
     * Routes constructor.
     *
     * @param App $app Slim App Instance
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Auto load all Routes
     * Call every protected methods begining with "load" (such as "loadTwig")
     *
     * Add your onwn Routes by creating methods begining with "load"
     */
    public function autoLoadRoutes()
    {
        $modelReflector = new \ReflectionClass(__CLASS__);
        $methods = $modelReflector->getMethods(\ReflectionMethod::IS_PROTECTED);
        foreach($methods as $method) {
            if (strrpos($method->name, 'load', -strlen($method->name)) !== false) {
                $this->{$method->name}();
            }
        }
    }

    /**
     * Add Slim App Routes
     * Override / Use this method to call specific functions
     */
    public function loadRoutes()
    {
        $this->loadFrontRoutes();
        $this->loadBackRoutes();
    }

    /**
     * Load front-office routes
     */
    protected function loadFrontRoutes()
    {
        $this->app->get('/', 'App\Controller\FrontController:homeAction')
            ->setName('homepage');
    }

    /**
     * Load back-office routes
     */
    protected function loadBackRoutes()
    {
        $this->app->get('/admin', 'App\Controller\BackController:dashboardAction')
            ->setName('dashboard');
    }

    /**
     * Load API routes
     */
    protected function loadApiRoutes()
    {
        $this->app->get('/api', 'App\Controller\ApiController:apiAction')
            ->setName('api');
    }
}