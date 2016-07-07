<?php
namespace App\Controller\Web;

use App\Core\Controller\WebController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Back
 * @package App\Controller\Web
 */
final class Back extends WebController
{
    /**
     * Dashboard renderer
     *
     * @param Request  $request     Slim Request
     * @param Response $response    Slim Response
     * @param array    $args        Arguments array (GET / POST / ...)
     *
     * @return Response
     */
    public function dashboardAction(Request $request, Response $response, $args)
    {
        return $this->render($response, 'pages/back/dashboard.twig');
    }
}
