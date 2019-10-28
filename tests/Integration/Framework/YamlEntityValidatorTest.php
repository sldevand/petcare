<?php

namespace Tests\Integration\Framework;

use Framework\Exception\YamlEntityNotValidException;
use Framework\Model\Validator\YamlEntityValidator;
use PHPUnit\Framework\TestCase;

/**
 * Class YamlEntityValidatorTest
 * @package Tests\Integration
 */
class YamlEntityValidatorTest extends TestCase
{
    /**
     * @throws YamlEntityNotValidException
     */
    public function testValid()
    {
        $validator = new YamlEntityValidator(__DIR__ . '/data/testValid.yaml');
        $valid = $validator->validate();

        self::assertTrue($valid === true);
        self::assertEmpty($validator->getErrors());
    }

    /**
     * @throws YamlEntityNotValidException
     */
    public function testMainKeysInvalid()
    {
        $this->expectException(YamlEntityNotValidException::class);
        $validator = new YamlEntityValidator(__DIR__ . '/data/testMainKeysInvalid.yaml');
        $validator->validate();
    }

    /**
     * @throws YamlEntityNotValidException
     */
    public function testMandatoryColumnDescriptionKeysInvalid()
    {
        $file = __DIR__ . '/data/testMandatoryColumnDescriptionKeysInvalid.yaml';
        $validator = new YamlEntityValidator($file);
        $valid = $validator->validate();
        $errors = $validator->getErrors();

        self::assertNotEmpty($errors, "There are no errors in this invalid file : $file");
        self::assertFalse($valid === true, "Validator has validated an this invalid file : $file");
    }
}
