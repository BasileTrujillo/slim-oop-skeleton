<?php
namespace App\Controller\Web;

use App\Core\Controller\WebController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Front
 * @package App\Controller\Web
 */
final class Front extends WebController
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
        // Add a debug bar message
        //$this->debugbar['messages']->info('Welcome to front controller');

        // Log an info
        $this->logger->info("Home page action dispatched");

        // Render
        return $this->render($response, 'pages/front/home.twig');
    }
}
