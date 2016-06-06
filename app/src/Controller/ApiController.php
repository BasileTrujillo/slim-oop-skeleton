<?php
namespace App\Controller;

use App\Core\BaseApiController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ApiController
 * @package App\Controller
 */
final class ApiController extends BaseApiController
{
    /**
     * Sample function that handle a JSON Response
     *
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     */
    public function apiAction(Request $request, Response $response, $args)
    {
        $data = ['data' => ['foo' => 'bar']];

        return $this->renderJson($response, $data);
    }
}
