<?php
namespace App\Controller;

use App\Core\BaseController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class BackController
 * @package App\Controller
 */
final class BackController extends BaseController
{
    /**
     * Dashboard renderer
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function dashboardAction(Request $request, Response $response, $args)
    {
        $this->logger->info("Dashbord page action dispatched");

        $this->render($response, 'pages/back/dashboard.twig');
        return $response;
    }
}
