<?php
namespace App\Core;

use PhpMiddleware\PhpDebugBar\PhpDebugBarMiddleware;
use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Middleware\HttpBasicAuthentication;
use Slim\Middleware\JwtAuthentication;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

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
        foreach ($methods as $method) {
            if (strrpos($method->name, 'load', -strlen($method->name)) !== false) {
                $this->{$method->name}();
            }
        }
    }

    /**
     * Load PHP whoops error if enabled
     * or load JSON or standard Slim error output
     *
     * @see https://github.com/zeuxisoo/php-slim-whoops
     * @see https://filp.github.io/whoops/
     */
    public function loadErrorHandler()
    {
        $settings = $this->dic->get('settings');
        if ($settings['displayErrorDetails'] === true) {
            if ($settings['error']['whoops'] === true) {
                $settings['debug'] = true; // Needed by WhoopsMiddleware
                $this->app->add(new WhoopsMiddleware);
            } elseif ($settings['error']['json'] === true) {
                $this->dic['errorHandler'] = function ($c) {
                    return function ($request, $response, $exception) use ($c) {
                        $data = [
                            'error' => [
                                'code'    => $exception->getCode(),
                                'message' => $exception->getMessage(),
                                'file'    => $exception->getFile(),
                                'line'    => $exception->getLine(),
                                'trace'   => explode("\n", $exception->getTraceAsString()),
                            ]
                        ];

                        return $response->withJson($data, 500);
                    };
                };
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

    /**
     * Setup Basic Auth using HttpBasicAuthentication and dependencie's autheticator
     * Setup JSON Web Token middleware to ensure API security
     */
    public function loadSecurityMiddlewares()
    {
        $container = $this->dic;
        $settings = $container->get('settings');

        // HttpBasicAuthentication
        $basicAuthOption = [
                "path" => "/api/auth",
                "authenticator" => $container->get('authenticator'),
                "error" => function (Request $request, Response $response, $arguments) {
                    $data["status"] = "error";
                    $data["message"] = $arguments["message"];
                    return $response->withJson($data);
                }
            ] + $settings['HttpBasicAuthentication'];

        $this->app->add(new HttpBasicAuthentication($basicAuthOption));

        // JwtAuthentication
        $jwtOptions = [
                "path" => "/api",
                "passthrough" => ["/api/auth"],
                "environment" => ["HTTP_X_TOKEN", "HTTP_AUTHORIZATION"],
                "header" => "X-Token",
                "logger" => $container->get('logger'),
                "error" => function (Request $request, Response $response, $arguments) {
                    $data["status"] = "error";
                    $data["message"] = $arguments["message"];
                    return $response->withJson($data);
                },
                "callback" => function (Request $request, Response $response, $arguments) use ($container) {
                    // Add the decoded token to DIC
                    $container['token'] = $arguments["decoded"];
                }
            ] + $settings['JwtAuthentication'];

        $this->app->add(new JwtAuthentication($jwtOptions));
    }
}