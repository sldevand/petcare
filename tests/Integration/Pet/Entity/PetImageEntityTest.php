<?php

namespace Tests\Integration\Pet\Entity;

use App\Modules\Pet\Model\Entity\PetImageEntity;
use Exception;
use Framework\Model\Validator\DefaultValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class PetImageEntityTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testValidatePetImageEntity()
    {
        $dotEnv = new Dotenv();
        $dotEnv->load(__DIR__.'/../../.env');

        $host = getenv('HOST');
        $catFile = __DIR__ . '/../data/cat.jpeg';
        copy($catFile, IMAGES_DIR.'/'.basename($catFile));
        $url = "http://$host/pets/1/image";

        $attributes = ['id' => 1, 'petId' => 1, 'image' => $url];
        $entity = new PetImageEntity($attributes);

        $validator = new DefaultValidator();
        $this->assertTrue($validator->validate($entity) === true);
    }
}
