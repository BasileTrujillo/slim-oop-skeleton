<?php
namespace App\Core\Controller;

use Monolog\Logger;
use Slim\Container;
use Slim\Http\Response;

/**
 * Class ApiController
 * Base API controller class with functions shared by all API controller implementations
 *
 * @package App\Core\Controller
 */
class ApiController
{
    /**
     * @var Logger Monolog Instance
     */
    protected $logger;

    /**
     * @var array Settings array
     */
    protected $settings;

    /**
     * @var \MongoDB\Client
     */
    protected $mongo;

    /**
     * @var array Decoded token array
     */
    protected $token;

    /**
     * @var array|null|object The authenticated user
     */
    protected $user;

    /**
     * Default controller construct
     *
     * @param Container $c Slim App Container
     */
    public function __construct(Container $c)
    {
        $this->logger   = $c->get('logger');
        $this->settings = $c->get('settings');
        $this->mongo    = $c->get('mongo_database');
        if ($c->offsetExists('token')) {
            $this->token = $c->get('token');
            $this->user  = $c->get('authenticator')->getUser(null, $this->token->sub);
        } else {
            $this->user  = $c->get('authenticator')->getUser();
        }
    }

    /**
     * Render a JSON response
     *
     * @param Response $response        Slim App Response
     * @param  mixed   $data            The data
     * @param  int     $status          The HTTP status code.
     * @param  int     $encodingOptions Json encoding options
     *
     * @return Response
     */
    protected function renderJson(Response $response, $data, $status = null, $encodingOptions = 0)
    {
        $jsonResponse = $response->withJson($data, $status, $encodingOptions);

        return $jsonResponse;
    }
}