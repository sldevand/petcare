<?php

namespace App\Modules\User\Controller;

use Exception;
use Framework\Controller\DefaultController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UserApiController
 * @package App\Modules\User\Controller
 */
class UserApiController extends DefaultController
{
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function get(Request $request, Response $response, $args = []): Response
    {
        try {
            $user = $this->getUserByApiKey($request);
        } catch (Exception $exception) {
            return $this->sendError($response, 'Error while fetching user!');
        }

        return $this->sendSuccess($response, 'User successfully fetched!', $user);
    }
}
