<?php
namespace App\Controller\Api;

use App\Core\Controller\ApiController;
use App\Model\Group;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class User
 * @package App\Controller\Api
 */
final class User extends ApiController
{    
    /**
     * Return logged in user informations
     *
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     */
    public function getUserAction(Request $request, Response $response, $args)
    {
        $data = [
            'login'         => $this->user['login'],
            'created_at'    => date('Y-m-d H:i:s', $this->user['created_at']),
            'updated_at'    => date('Y-m-d H:i:s', $this->user['updated_at'])
        ];

        //Recuperation du group de l'utilisateur
        $group = new Group($this->mongo);
        $groupDoc = $group->findById($this->user['group_id']);

        if ($groupDoc !== null) {
            $data['group_name'] = $groupDoc['name'];
            $data['group_id']   = (string) $groupDoc['_id'];
        }

        return $this->renderJson($response, $data);
    }
}
