<?php

namespace Tests\Integration\Pet\Provider;

use App\Modules\Pet\Model\Entity\PetEntity;
use DateTime;
use Exception;

/**
 * Class PetEntityProvider
 * @package Tests\Integration\Pet\Provider
 */
class PetEntityProvider
{
    /**
     * @return array
     * @throws Exception
     */
    public static function getPets(): array
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $dogImageEntity = PetImageEntityProvider::getPetImages()['dog'];
        $dogAttributes = [
            'name' => 'waf', 'dob' => '13/10/2014', 'specy' => 'dog', 'createdAt' => $now, 'image' => $dogImageEntity
        ];
        $dogEntity = new PetEntity($dogAttributes);

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $catImageEntity = PetImageEntityProvider::getPetImages()['cat'];
        $attributesToUpdate = [
            'name' => 'elie', 'dob' => '15/10/2014', 'specy' => 'cat', 'createdAt' => $now, 'image' => $catImageEntity
        ];
        $catEntity = new PetEntity($attributesToUpdate);

        return [
            'waf'  => $dogEntity,
            'elie' => $catEntity
        ];
    }
}
