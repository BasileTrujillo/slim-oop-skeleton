<?php
namespace App\Core;

use DebugBar\StandardDebugBar;
use Monolog\Logger;
use Slim\Container;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class BaseController
 * Base controller class with functions shared by all controller implementations
 *
 * @package App\Core
 */
class BaseController
{
    /**
     * @var Twig App View
     */
    protected $view;

    /**
     * @var Logger Monolog Instance
     */
    protected $logger;

    /**
     * @var Messages Slim Flash Message Instance
     */
    protected $flash;

    /**
     * @var array Settings array
     */
    protected $settings;

    /**
     * @var array Default data passed throught view renderer
     */
    protected $defaultData;

    /**
     * @var StandardDebugBar Debug Bar Instance if enabled
     */
    protected $debugbar;

    /**
     * Default controller construct
     *
     * @param Container $c Slim App Container
     */
    public function __construct(Container $c)
    {
        $this->view         = $c->get('view');
        $this->logger       = $c->get('logger');
        $this->flash        = $c->get('flash');
        $this->settings     = $c->get('settings');

        if ($this->settings['debugbar']['enabled'] === true) {
            $this->debugbar = $c->get('debugbar');
        }

        //Default data to pass trought twig tpl
        $this->defaultData = array(
            'settings'  => $this->settings
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