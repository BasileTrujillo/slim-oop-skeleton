<?php
namespace App\CLI;

use App\Core\BaseCliController;

/**
 * Class Setup
 * Sample class to setup something using CLI
 *
 * @package App\CLI
 */
final class Setup extends BaseCliController
{
    /**
     * @var bool InitAll Flag
     */
    private $initAll = false;

    /**
     * Initialize setup
     */
    public function init()
    {
        $this->printOut('Init Function called from Setup CLI Controller.');

        if ($this->initAll) {
            $this->printOut('-a or --all argument provided');
        }
    }

    /**
     * Custom parameter check
     *
     * @return bool Return false will automaticaly call `printHelp()` function and stop script execution
     */
    public function checkParameters()
    {
        // Add custom parameter
        $this->addParameter('a', 'all', 'Setup All', '--all');

        if(parent::checkParameters()) {
            // Check custom parameter
            $aOpt = $this->getArg('a');
            if ($aOpt !== null) {
                $this->initAll = true;
            }
            return true;
        } else {
            return false;
        }
    }
}
