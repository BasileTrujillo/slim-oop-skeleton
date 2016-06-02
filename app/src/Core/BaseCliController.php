<?php
namespace App\Core;

use Monolog\Logger;
use Slim\Container;
use League\CLImate\CLImate;

/**
 * Class BaseCliController
 * Base CLI controller class with functions shared by all CLI controller implementations
 *
 * @package App\Core
 */
class BaseCliController
{
    /**
     * @var Logger Monolog Instance
     */
    protected $logger;

    /**
     * @var CLImate
     */
    protected $climate;

    /**
     * @var array getOpt() parameters: keys are short opt and values are long opt
     */
    private $parameters = array();

    /**
     * @var array Store argument documentation here. It wil be displayed by printHelp()
     *            Keys: (String) short opt | Values: ['doc' => (String), 'example' => (String)]
     */
    private $helpParameters = array();

    /**
     * @var array argument list provided by getOpt() function from checkParameters()
     */
    protected $args;

    /**
     * @var bool Enable or disable verbosity
     */
    private $isVerbose = false;


    /**
     * Default controller construct
     *
     * @param Container $c Slim App Container
     */
    public function __construct(Container $c)
    {
        $this->logger       = $c->get('logger');
        $this->climate      = $c->get('climate');
    }

    /**
     * Default parameter check
     * Extend it to add your own parameters
     *
     * @return bool If false, BootstrapCLI will call printHelp() function then stop process
     */
    public function checkParameters()
    {
        $this->addParameter('s:'   , 'service:'    , 'Service name [class]::[method]', '-s Setup::init');
        $this->addParameter('h'    , 'help'        , 'Print this help text', '-h');
        $this->addParameter('v'    , 'verbose'     , 'Enable verbosity (disabled by default)', '-v');

        $this->args = getopt(implode('', array_keys($this->parameters)), array_values($this->parameters));

        if (isset($this->args['v']) || isset($this->args['verbose'])) {
            $this->isVerbose = true;
        }

        if (isset($this->args['h']) || isset($this->args['help'])) {
            return false;
        }

        return true;
    }

    /**
     * Add an argument to check through getOpt()
     *
     * @param string $short     Short Opt
     * @param string $long      Long Opt
     * @param string $doc       Documentation printed by printHelp()
     * @param string $example   Exemple printed by printHelp()
     */
    protected function addParameter($short, $long, $doc = '', $example = '')
    {
        $this->parameters[$short] = $long;
        $this->helpParameters[$short] = [
            'doc'       => $doc,
            'example'   => $example
        ];
    }

    /**
     * Get Argument returned by getOpt()
     * Check by short opt and long opt
     *
     * @param string $shortOpt Short Opt (must be the same as provided to addParameter()
     *
     * @return string|null
     */
    protected function getArg($shortOpt)
    {
        // Check Short Opt
        if(isset($this->args[$shortOpt])) {
            return $this->args[$shortOpt];
        // Check Long Opt
        } elseif (isset($this->args[$this->parameters[$shortOpt]])) {
            return $this->args[$this->parameters[$shortOpt]];
        // Return null if no one found
        } else {
            return null;
        }

    }

    /**
     * $isVerbose getter
     */
    public function isVerbose()
    {
        return $this->isVerbose;
    }

    /**
     * Print default help (usage and argument)
     */
    public function printHelp()
    {
        $this->climate
            ->out('CLI Help:')->br()
            ->out('Usage:')
            ->out('php bin/cli.php -s [class]::[method] [options]')->br()
            ->out('Options:');

        $options = array();
        foreach($this->parameters as $short => $long)
        {
            $argType = 'Do not accept values';

            if (strpos($short, '::') !== false) {
                $argType = 'Optional value';
            } else {
                if (strpos($short, ':') !== false) {
                    $argType = 'Parameter requires value';
                }
            }

            $options[] = [
                'Argument'      => "-".str_replace(':', '', $short).", --".str_replace(':', '', $long),
                'Type'          => $argType,
                'Description'   => isset($this->helpParameters[$short]) ? $this->helpParameters[$short]['doc'] : '',
                'Example'       => isset($this->helpParameters[$short]) ? $this->helpParameters[$short]['example'] : '',
            ];
        }

        $this->climate->table($options);
    }

    /**
     * Print string through CLImate if verbosity is enabled
     *
     * @param mixed $var    Variable to print, array is rendered in a table view
     * @param bool  $dump   If TRUE, render dumped $var
     * @param bool  $log    If TRUE, log $var using monolog
     */
    protected function printOut($var, $dump = false, $log = false)
    {
        if ($this->isVerbose()) {
            if ($dump) {
                $this->climate->dump($var);
            } elseif (is_array($var) && isset($var[0]) && is_array($var[0])) {
                $this->climate->table($var);
            } else {
                $this->climate->out($var);
            }
        }

        if ($log) {
            if ($dump) {
                $var = print_r($var, true);
            }
            $this->logger->info($var);
        }
    }
}