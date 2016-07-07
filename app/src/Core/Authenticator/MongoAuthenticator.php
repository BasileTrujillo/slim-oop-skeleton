<?php
namespace App\Core\Authenticator;

use MongoDB\BSON\ObjectID;
use Slim\Middleware\HttpBasicAuthentication\AuthenticatorInterface;

/**
 * Class MongoAuthenticator
 * @package App\Core\Authenticator
 */
class MongoAuthenticator implements AuthenticatorInterface
{
    /**
     * @var array Default options
     */
    private $options = [
        'database' => 'db',
        'user_collection' => 'users',
        'user_field' => 'login',
        'hash_field' => 'hash',
    ];

    /**
     * @var \MongoDB\Database Parent database from SaaS architecture
     */
    private $parentDB;

    /**
     * @var \MongoDB\Collection Parent database user collection
     */
    private $userCollection;

    /**
     * @var array|object|null Authenticated user
     */
    private $user;

    /**
     * MongoAuthenticator constructor.
     *
     * @param \MongoDB\Client   $mongoClient
     * @param array             $options
     */
    public function __construct(\MongoDB\Client $mongoClient, array $options = [])
    {
        if ($options) {
            $this->options = array_merge($this->options, $options);
        }

        $this->parentDB = $mongoClient->selectDatabase($this->options['database']);
        $this->userCollection = $this->parentDB->selectCollection($this->options['user_collection']);
    }

    /**
     * MongoAuthenticator Invoke
     *
     * @param array $arguments
     *
     * @return bool
     */
    function __invoke(array $arguments)
    {
        $this->getUser($arguments['user']);

        if ($this->user !== null) {
            if (password_verify($arguments['password'], $this->user[$this->options['hash_field']])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find and get User from DB if not already done
     *
     * @param string        $login  Search by login
     * @param null|string   $_id    If _id is passed, it will search with this only criteria
     *
     * @return array|null|object
     */
    public function getUser($login = null, $_id = null)
    {
        if($this->user === null)
        {
            if ($_id !== null) {
                $find = ['_id' => new ObjectID($_id)];
            } else {
                $find = [$this->options['user_field'] => $login];
            }

            $this->user = $this->userCollection->findOne($find);
        }
        return $this->user;
    }

}