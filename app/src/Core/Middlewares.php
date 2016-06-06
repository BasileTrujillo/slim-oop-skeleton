<?php
namespace App\Core;

use PhpMiddleware\PhpDebugBar\PhpDebugBarMiddleware;
use Slim\App;
use Slim\Container;

/**
 * Class Middlewares
 * Register Slim middlewares into provided App object
 *
 * @package App\Core
 */
class Middlewares
{
    /**
     * @var App Slim App instance
     */
    private $app;

    /**
     * @var Container Slim DI Container
     */
    private $dic;

    /**
     * Middlewares constructor.
     *
     * @param App $app Slim App Instance
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->dic = $this->app->getContainer();
    }

    /**
     * Auto load all Middlewares
     * Call every protected methods begining with "load" (such as "loadTwig")
     *
     * Add your onwn Middlewares by creating methods begining with "load"
     */
    public function autoLoadMiddlewares()
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
     * Load Debug Bar Javascript Renderer if enabled
     */
    public function loadDebugBar()
    {
        if ($this->dic->get('settings')['debugbar']['enabled'] === true) {
            $this->app->add(new PhpDebugBarMiddleware(
                $this->dic->get('debugbar')->getJavascriptRenderer('/phpdebugbar')
            ));
        }
    }
}