<?php

namespace App\Modules\User\Controller;

use App\Modules\User\Helper\ApiKey;
use App\Modules\User\Model\Repository\UserRepository;
use Exception;
use Framework\Api\Repository\RepositoryInterface;
use Framework\Controller\DefaultController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UserApiController
 * @package App\Modules\User\Controller
 */
class UserApiController extends DefaultController
{
    /** @var \App\Modules\User\Helper\ApiKey */
    protected $apiKeyHelper;

    /**
     * UserApiController constructor.
     * @param RepositoryInterface $repository
     * @param UserRepository $userRepository
     * @param ApiKey $apiKeyHelper
     */
    public function __construct(
        RepositoryInterface $repository,
        UserRepository $userRepository,
        ApiKey $apiKeyHelper
    ) {
        parent::__construct($repository, $userRepository);
        $this->apiKeyHelper = $apiKeyHelper;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function get(Request $request, Response $response, $args = []): Response
    {
        try {
            $user = $this->apiKeyHelper->getUserByApiKey($request);
        } catch (Exception $exception) {
            return $this->sendError($response, 'Error while fetching user!');
        }

        return $this->sendSuccess($response, 'User successfully fetched!', $user);
    }
}
