<?php
namespace App\Controller;

use App\Core\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;

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
        $this->render($response, 'pages/front/home.twig');
        return $response;
    }
}
