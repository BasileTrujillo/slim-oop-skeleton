<?php
namespace App\Core;

use Monolog\Logger;
use Slim\Container;
use Slim\Http\Response;

/**
 * Class BaseApiController
 * Base API controller class with functions shared by all API controller implementations
 *
 * @package App\Core
 */
class BaseApiController
{
    /**
     * @var Logger Monolog Instance
     */
    protected $logger;

    /**
     * Default controller construct
     *
     * @param Container $c Slim App Container
     */
    public function __construct(Container $c)
    {
        $this->logger       = $c->get('logger');
    }

    /**
     * Render a JSON response
     *
     * @param Response $response        Slim App Response
     * @param  mixed  $data             The data
     * @param  int    $status           The HTTP status code.
     * @param  int    $encodingOptions  Json encoding options
     *
     * @return Response
     */
    protected function renderJson(Response $response, $data, $status = null, $encodingOptions = 0)
    {
        $jsonResponse = $response->withJson($data, $status, $encodingOptions);
        return $jsonResponse;
    }
}