<?php

namespace Framework\Model\Validator;

use Framework\Exception\YamlEntityNotValidException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlEntityValidator
 * @package Framework\Model\Validator
 */
class YamlEntityValidator
{
    const MANDATORY_MAIN_KEYS = ['table', 'fields'];
    const MANDATORY_COLUMN_DESCRIPTION_KEYS = ['column', 'type'];
    const OPTIONAL_COLUMN_DESCRIPTION_KEYS = ['constraints'];

    /** @var array */
    protected $yaml;

    /** @var array */
    protected $errors = [];

    /**
     * YamlValidator constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->yaml = Yaml::parseFile($file);
    }



    /**
     * @return bool
     * @throws YamlEntityNotValidException
     */
    public function validate(): bool
    {
        $this->validateMainKeys();

        try {
            $this->validateFieldsKeys();
        } catch (YamlEntityNotValidException $exception) {
            $this->errors[] = $exception->getMessage();
        }

        return empty($this->errors);
    }

    /**
     * @throws YamlEntityNotValidException
     */
    protected function validateMainKeys()
    {
        $mainKeys = array_keys($this->yaml);
        if (!empty(array_diff($mainKeys, self::MANDATORY_MAIN_KEYS))) {
            $mandatoryMainKeys = implode(' , ', self::MANDATORY_MAIN_KEYS);
            throw new YamlEntityNotValidException(
                "Main keys are not valid, mandatory keys are $mandatoryMainKeys"
            );
        }
    }

    /**
     * @return bool
     * @throws YamlEntityNotValidException
     */
    protected function validateFieldsKeys(): bool
    {
        $fields = $this->yaml['fields'];
        foreach ($fields as $property => $columnDescription) {
            $columnDescriptionKeys = array_keys($columnDescription);
            $diffMandatoryColumnDescriptionKeys = array_diff(
                $columnDescriptionKeys,
                self::MANDATORY_COLUMN_DESCRIPTION_KEYS
            );

            if (!empty($diffMandatoryColumnDescriptionKeys)) {
                $diffOptionalColumnDescriptionKeys = array_diff(
                    $diffMandatoryColumnDescriptionKeys,
                    self::OPTIONAL_COLUMN_DESCRIPTION_KEYS
                );

                if (!empty($diffOptionalColumnDescriptionKeys)) {
                    $optionalColumnDescriptionKeys = implode(',', self::OPTIONAL_COLUMN_DESCRIPTION_KEYS);
                    $mandatoryColumnDescriptionKeys = implode(',', self::MANDATORY_COLUMN_DESCRIPTION_KEYS);
                    throw new YamlEntityNotValidException(
                        "In property $property --> Column Description Keys are not valid:
                         mandatory keys are | $mandatoryColumnDescriptionKeys |
                         optional  keys are | $optionalColumnDescriptionKeys |"
                    );
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getYaml(): array
    {
        return $this->yaml;
    }
}
