<?php

namespace App\Modules\Token\Helper;

use Firebase\JWT\JWT;
use Framework\Api\Entity\EntityInterface;
use Framework\Helper\DateHelper;

/**
 * Class Token
 * @package App\Modules\Token\Helper
 */
class Token
{
    /**
     * @param EntityInterface $user
     * @param string $secret
     * @return string
     * @throws \Exception
     */
    public function generate(EntityInterface $user, string $secret): string
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
