<?php

namespace Tests\Integration\Pet\Provider;

use App\Modules\Pet\Model\Entity\PetImageEntity;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class PetImageEntityProvider
 * @package Tests\Integration\Pet\Provider
 */
class PetImageEntityProvider extends TestCase
{
    /**
     * @throws Exception
     */
    public static function getPetImages()
    {
        $files = glob(__DIR__ . "/../data/*");
        $petImages = [];
        foreach ($files as $file) {
            $name = explode('.', basename($file))[0];
            $encodedImage = base64_encode(file_get_contents($file));
            $petImages[$name] = new PetImageEntity(['image' => $encodedImage]);
        }

        return $petImages;
    }
}
