<?php

namespace Tests\Integration\Pet;

use App\Modules\Pet\Model\Entity\PetEntity;
use Exception;
use Framework\Model\Validator\DefaultValidator;
use PHPUnit\Framework\TestCase;

class PetValidationTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testValidatePetEntity()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $attributes = [
            'name' => 'elie', 'dob' => '13/10/2014', 'specy' => 'cat', 'imageId' => 1, "createdAt" => $now
        ];
        $entity = new PetEntity($attributes);

        $validator = new DefaultValidator();
        $this->assertTrue($validator->validate($entity) === true);
    }
}
