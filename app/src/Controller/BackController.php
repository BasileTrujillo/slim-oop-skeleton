<?php
namespace App\Controller;

use App\Core\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BackController
 * @package App\Controller
 */
final class BackController extends BaseController
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
