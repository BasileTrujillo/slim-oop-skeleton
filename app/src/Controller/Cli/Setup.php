<?php
namespace App\Controller\Cli;

use App\Core\Controller\CliController;
use App\Model\Group;
use App\Model\User;
use Slim\Container;

/**
 * Class Setup
 * Sample class to setup something using CLI
 *
 * @package App\Controller\Cli
 */
final class Setup extends CliController
{
    /**
     * @var \MongoDB\Database
     */
    protected $mongodb;

    /**
     * Default controller construct
     *
     * @param Container $c Slim App Container
     */
    public function __construct(Container $c)
    {
        $this->mongodb = $c->get('mongo_database');
        
        return parent::__construct($c);
    }
    
    /**
     * Initialize setup - Auto init models
     * Creates collections, validators and indexes
     */
    public function init()
    {
        $models = [
            'User',
            'Group'
        ];

        foreach ($models as $model) {
            $className = 'App\Model\\'.$model;
            if(class_exists($className)) {
                $this->printOut('Create '.$model.' collection');
                $class = new $className($this->mongodb);
                $class->createCollection();
                $class->createIndexes();
            } else {
                $this->climate->error('Cannot create '.$model.' collection: Class '.$className.' does not exists.');
            }
        }
    }


    /**
     * Add or Update an User and his group
     */
    public function user()
    {
        $login = $this->getArg('l');
        $passwd = $this->getArg('p');
        $groupName = $this->getArg('g');

        if(empty($login) || empty($passwd) || empty($groupName)) {
            $this->climate->error('Missing parameter');
            return false;
        }

        $this->printOut('Create or update Group: '.$groupName);
        $g = new Group($this->mongodb);
        $docGroup = $g->findOne(['name' => $groupName]);
        $groupId = null;
        if ($docGroup === null) {
            $docGroup = $g->upsert(
                [
                    'name' => $groupName
                ],
                'name'
            );

            $groupId = $docGroup->getUpsertedId();

        } else {
            $groupId = $docGroup['_id'];
        }

        $this->printOut('Create or update User: '.$login);
        $user = new User($this->mongodb);
        $user->upsert(
            [
                'login' => $login,
                'hash' => password_hash($passwd, PASSWORD_BCRYPT),
                'group_id' => $groupId
            ],
            'login'
        );
    }

    /**
     * Custom parameter check
     *
     * @return bool Return false will automaticaly call `printHelp()` function and stop script execution
     */
    public function checkParameters()
    {
        // User parameters
        $this->addParameter('l:', 'login:', 'Login', '-l foo');
        $this->addParameter('p:', 'passwd:', 'Password', '-p bar');
        $this->addParameter('g:', 'group:', 'Group name', '-g baz');

        return parent::checkParameters();
    }
}
