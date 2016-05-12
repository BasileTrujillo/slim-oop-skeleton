<?php
namespace App\Controller;

use App\Core\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class FrontController
 * @package App\Controller
 */
final class FrontController extends BaseController
{
    /**
     * Home renderer
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function homeAction(Request $request, Response $response, $args)
    {
        $this->logger->info("Home page action dispatched");

        $this->render($response, 'pages/front/home.twig');
        return $response;
    }
}
