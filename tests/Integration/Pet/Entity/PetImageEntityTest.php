<?php

namespace Tests\Integration\Pet\Entity;

use App\Modules\Pet\Model\Entity\PetImageEntity;
use Exception;
use Framework\Model\Validator\DefaultValidator;
use PHPUnit\Framework\TestCase;

class PetImageEntityTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testValidatePetImageEntity()
    {
        $catFile = __DIR__ . '/../data/cat.jpeg';
        $contents = file_get_contents($catFile);

        $base64Image = base64_encode($contents);
        $attributes = ['id' => 1, 'image' => $base64Image];
        $entity = new PetImageEntity($attributes);

        $validator = new DefaultValidator();
        $this->assertTrue($validator->validate($entity) === true);
    }
}
