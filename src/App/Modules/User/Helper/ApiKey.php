<?php

namespace App\Modules\User\Helper;

use App\Modules\User\Model\Repository\UserRepository;
use Exception;
use Framework\Api\Entity\EntityInterface;
use Slim\Http\Request;

class ApiKey
{
    /** @var UserRepository */
    protected $userRepository;

    /**
     * ApiKey constructor.
     * @param \App\Modules\User\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param \Slim\Http\Request $request
     * @return string
     * @throws \Exception
     */
    protected function getApiKey(Request $request): string
    {
        $authHeaderArr = $request->getHeader('Authorization');
        if (empty($authHeaderArr) && count($authHeaderArr) !== 1) {
            throw new Exception('No authorization header found');
        }

        $authorizationHeaderArr = explode(' ', $authHeaderArr[0]);

        if (empty($authorizationHeaderArr) && count($authorizationHeaderArr) !== 2) {
            throw new Exception('No authorization header found');
        }

        return $authorizationHeaderArr[1];
    }

    /**
     * @param \Slim\Http\Request $request
     * @return \Framework\Api\Entity\EntityInterface
     * @throws \Exception
     */
    public function getUserByApiKey(Request $request): EntityInterface
    {
        $apiKey = $this->getApiKey($request);
        return $this->userRepository->fetchByApiKey($apiKey);
    }
}
