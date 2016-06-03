<?php
namespace App\Core;

/**
 * Class Bootstrap
 * Load and setup Slim App and DIC
 *
 * @package App\Core
 */
class BootstrapCLI extends Bootstrap
{
    /**
     * @var int Start microtime for profiling
     */
    private $startMicrotime;

    /**
     * @var int End microtime for profiling
     */
    private $endMicrotime;

    /**
     * @var string CLI Controller namespace
     */
    private $namespace = '\\App\\CLI\\';

    /**
     * Setup and Run
     */
    public function run()
    {
        // Start profiling if enabled
        $profilingEnabled = $this->app->getContainer()->get('settings')['cli']['profiling'];
        if ($profilingEnabled) {
            $this->startMicrotime = microtime();
        }

        // Set up dependencies
        $this->loadDependencies();

        // Check CLI service
        $service = $this->getCLIService();

        // Run CLI service
        $this->runCLIService($service);

        // End and display profiling if enabled
        if ($profilingEnabled) {
            $this->endMicrotime = microtime();
            $this->printProfiling();
        }
    }

    /**
     * Set Up CLI Dependencies
     */
    protected function loadDependencies()
    {
        $this->dicDependencies = new Dependencies($this->app);
        $this->dicDependencies->loadMonolog();
        $this->dicDependencies->loadCLImate();
        $this->dicDependencies->loadPDO();
    }

    /**
     * Get service name form first arg
     * Provided sevice name must be like ClassName::MethodName
     *
     * @return array [0] => Class Name | [1] => Method Name
     *
     * @throws \ErrorException
     */
    private function getCLIService()
    {
        $parameters = array(
            's:' => 'service:',
        );

        $options = getopt(implode('', array_keys($parameters)), array_values($parameters));

        if (
            (isset($options['s']) && !empty($options['s'])) ||
            (isset($options['service']) && !empty($options['service']))
        ) {
            $service = !empty($options['s']) ? $options['s'] : $options['service'];
            $service = explode('::', $service);
            if (count($service) == 2) {
                return $service;
            } else {
                throw new \ErrorException(
                    'Service (-s | --service) arguments is missing.
                    Usage :
                    php bin/cli.php -s [class]::[method] [options]'
                );
            }
        } else {
            throw new \ErrorException(
                'Service (-s | --service) arguments is missing.
                Usage :
                php bin/cli.php -s [class]::[method] [options]'
            );
        }
    }

    /**
     * Run CLI Service if exist
     * Call checkParameters() function
     * Then call wished function
     *
     * @param array $service
     *
     * @throws \ErrorException
     */
    private function runCLIService($service)
    {
        $className = $this->namespace.$service[0];
        if (class_exists($className)) {
            if (method_exists($className, $service[1])) {
                $controller = new $className($this->app->getContainer());
                if ($controller->checkParameters()) {
                    $controller->{$service[1]}();
                } else {
                    $controller->printHelp();
                }
            } else {
                throw new \ErrorException(
                    'Method name "'.$service[1].'" not found in "'.$className.'"!'
                );
            }
        } else {
            throw new \ErrorException(
                'No CLI Controller named "'.$className.'" found!'
            );
        }
    }

    /**
     * Print Profiling informations
     */
    private function printProfiling()
    {
        $profiler = [
            [
                'Execution time' => ($this->endMicrotime - $this->startMicrotime) . 's',
                'Used memory' => $this->convertBytes(memory_get_usage()),
                'Allocated memory' => $this->convertBytes(memory_get_usage(true)),
                'Peak of memory' => $this->convertBytes(memory_get_peak_usage())
            ]
        ];
        $climate = $this->app->getContainer()->get('climate');
        $climate->out('Profiling:');
        $climate->table($profiler);
    }

    private function convertBytes($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),3).' '.$unit[$i];
    }
}