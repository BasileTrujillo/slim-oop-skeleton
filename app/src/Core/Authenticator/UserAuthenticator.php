<?php
namespace App\Core\Authenticator;

use App\Model\User;
use Slim\Middleware\HttpBasicAuthentication\AuthenticatorInterface;

/**
 * Class UserAuthenticator
 * @package App\Core\Authenticator
 */
class UserAuthenticator implements AuthenticatorInterface
{
    /**
     * @var array Default options
     */
    private $options = [
        'user_field' => 'login',
        'hash_field' => 'hash',
    ];

    /**
     * @var \App\Model\User User Model
     */
    private $userModel;

    /**
     * @var array|object|null Authenticated user
     */
    private $user;

    /**
     * UserAuthenticator constructor.
     *
     * @param \App\Model\User   $user
     */
    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    /**
     * UserAuthenticator Invoke
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
                $this->user = $this->userModel->findById($_id);
            } else {
                $this->user = $this->userModel->findOne([$this->options['user_field'] => $login]);
            }
        }
        return $this->user;
    }

}