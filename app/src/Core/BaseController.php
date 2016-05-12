<?php
namespace App\Core;

use Psr\Log\LoggerInterface;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Base controller class with functions shared by all controller implementations
 * @author Basile Trujillo
 */
class BaseController
{
    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Array
     */
    protected $settings;

    /**
     * @var Array
     */
    protected $defaultData;

    /**
     * Default controller construct
     *
     * @param Twig            $view
     * @param LoggerInterface $logger
     * @param                 $settings
     */
    public function __construct(Twig $view, LoggerInterface $logger, $settings)
    {
        $this->view         = $view;
        $this->logger       = $logger;
        $this->settings     = $settings;

        //Default data to pass trought twig tpl
        $this->defaultData = array(
            'settings'  => $this->settings,
            //'asset.min' => $this->settings['mode'] == 'production' ? '' : '.min'
        );
    }

    /**
     * Render twig template with merged datas
     *
     * @param Response $response    Slim Response
     * @param string   $tpl         Twig template to load
     * @param array    $data        Data to send into loaded view
     */
    protected function render(Response $response, $tpl, $data = array())
    {
        $datas = $data + $this->defaultData;
        $this->view->render($response, $tpl, $datas);
    }
}