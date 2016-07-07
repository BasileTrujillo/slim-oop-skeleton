<?php
namespace App\Controller\Api;

use App\Core\Controller\ApiController;
use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Auth
 * @package App\Controller\Api
 */
final class Auth extends ApiController
{
    /**
     * Authentication endpoint: provide a JSON Web Token on succes
     *
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     */
    public function authAction(Request $request, Response $response, $args)
    {
        $now = new \DateTime();
        $future = new \DateTime("now +2 hours");        
        $jti = base64_encode(random_bytes(16));
        $payload = [
            "iat" => $now->getTimestamp(),
            "exp" => $future->getTimestamp(),
            "jti" => $jti,
            "sub" => (string)$this->user['_id'],
        ];
        $secret = $this->settings['JwtAuthentication']['secret'];
        $token = JWT::encode($payload, $secret, "HS256");
        $data["status"] = "ok";
        $data["token"] = $token;

        return $this->renderJson($response, $data, 201);
    }
}
