<?php

namespace Tests\Integration\Pet\Provider;

use App\Modules\Pet\Model\Entity\PetImageEntity;
use Exception;

/**
 * Class PetImageEntityProvider
 * @package Tests\Integration\Pet\Provider
 */
class PetImageEntityProvider
{
    /**
     * @throws Exception
     */
    public static function getPetImages()
    {
        $files = glob(__DIR__ . "/../data/*");
        $petImages = [];
        foreach ($files as $key => $file) {
            $name = explode('.', basename($file))[0];
            $encodedImage = base64_encode(file_get_contents($file));
            $petImages[$name] = new PetImageEntity(['image' => $encodedImage, 'petId' => $key + 1]);
        }

        return $petImages;
    }
}
