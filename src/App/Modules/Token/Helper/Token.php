<?php

namespace App\Modules\Token\Helper;

use App\Modules\User\Model\Entity\UserEntity;
use Firebase\JWT\JWT;
use Framework\Helper\DateHelper;

/**
 * Class Token
 * @package App\Modules\Token\Helper
 */
class Token
{
    /**
     * @param UserEntity $user
     * @param string $secret
     * @return string
     * @throws \Exception
     */
    public function generate($user, $secret)
    {
        return JWT::encode(
            [
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'iat' => DateHelper::now()
            ],
            $secret,
            "HS256"
        );
    }
}
