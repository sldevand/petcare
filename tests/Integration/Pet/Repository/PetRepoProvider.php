<?php

namespace Tests\Integration\Pet\Repository;

use App\Modules\Pet\Model\Entity\PetImageEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class PetRepoProvider
 * @package Tests\Integration\Pet\Repository
 */
class PetRepoProvider extends TestCase
{
    /**
     * @throws \Exception
     */
    protected static function getPetImages()
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
