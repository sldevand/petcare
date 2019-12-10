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
    const OPTIONAL_CONSTRAINTS_KEYS = ['nullable', 'fk', 'unique','minLength', 'maxLength', 'pattern','filter'];
    const MANDATORY_FK_KEYS = ['reference', 'table', 'cascade'];

    /** @var string */
    protected $file;

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
        $this->file = $file;
    }

    /**
     * @return bool
     * @throws YamlEntityNotValidException
     */
    public function validate(): bool
    {
        $this->validateMainKeys();
        $this->validateFieldsKeys();

        return empty($this->errors);
    }

    /**
     * @throws YamlEntityNotValidException
     */
    protected function validateMainKeys()
    {
        $mainKeys = array_keys($this->yaml);
        if (!empty(array_diff($mainKeys, self::MANDATORY_MAIN_KEYS))) {
            $this->throwMainKeysException();
        }
    }

    /**
     * @return bool
     */
    protected function validateFieldsKeys(): bool
    {
        $fields = $this->yaml['fields'];
        foreach ($fields as $property => $columnDescription) {
            try {
                $mandatoryColumnDescriptionKeys = $this->getMandatoryColumnDescriptionKeys($columnDescription);
                if (empty($mandatoryColumnDescriptionKeys)) {
                    continue;
                }

                $optionalColumnDescriptionKeys = $this->getOptionalColumnDescriptionKeys($mandatoryColumnDescriptionKeys);
                if (!empty($optionalColumnDescriptionKeys)) {
                    $this->throwColumnDescriptionKeyException($property);
                }

                $optionalConstraintKeys = $this->getOptionalConstraintKeys($columnDescription['constraints']);
                if (!empty($optionalConstraintKeys)) {
                    $this->throwOptionalConstraintKeysException($property);
                }

                if (empty($columnDescription['constraints']['fk'])) {
                    continue;
                }

                $mandatoryFkKeys = $this->getMandatoryFkKeys($columnDescription['constraints']['fk']);
                if (!empty($mandatoryFkKeys)) {
                    $this->throwMandatoryFkKeysException($property);
                }
            } catch (YamlEntityNotValidException $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        return true;
    }

    /**
     * @param array $columnDescription
     * @return array
     */
    protected function getMandatoryColumnDescriptionKeys($columnDescription)
    {
        $columnDescriptionKeys = array_keys($columnDescription);
        return array_diff(
            $columnDescriptionKeys,
            self::MANDATORY_COLUMN_DESCRIPTION_KEYS
        );
    }

    /**
     * @param array $mandatoryColumnDescriptionKeys
     * @return array
     */
    protected function getOptionalColumnDescriptionKeys($mandatoryColumnDescriptionKeys)
    {
        return array_diff(
            $mandatoryColumnDescriptionKeys,
            self::OPTIONAL_COLUMN_DESCRIPTION_KEYS
        );
    }

    /**
     * @param array $fk
     * @return array
     */
    protected function getMandatoryFkKeys($fk)
    {
        $fkKeys = array_keys($fk);
        return array_diff(
            $fkKeys,
            self::MANDATORY_FK_KEYS
        );
    }


    /**
     * @throws YamlEntityNotValidException
     */
    protected function throwMainKeysException()
    {
        $mandatoryMainKeys = implode(' , ', self::MANDATORY_MAIN_KEYS);
        throw new YamlEntityNotValidException(
            "In file $this->file :
                      Main keys are not valid! 
                      Mandatory keys are | $mandatoryMainKeys |"
        );
    }

    /**
     * @param string $property
     * @throws YamlEntityNotValidException
     */
    protected function throwColumnDescriptionKeyException(string $property)
    {
        $mandatoryColumnDescriptionKeys = implode(',', self::MANDATORY_COLUMN_DESCRIPTION_KEYS);
        $optionalColumnDescriptionKeys = implode(',', self::OPTIONAL_COLUMN_DESCRIPTION_KEYS);
        throw new YamlEntityNotValidException(
            "In file $this->file : 
                      In property $property, Column Description Keys are not valid !
                      Mandatory keys are | $mandatoryColumnDescriptionKeys |
                      Optional  keys are | $optionalColumnDescriptionKeys |"
        );
    }


    /**
     * @param string $property
     * @throws YamlEntityNotValidException
     */
    protected function throwOptionalConstraintKeysException(string $property)
    {
        $optionalConstraintKeys = implode(',', self::OPTIONAL_CONSTRAINTS_KEYS);
        throw new YamlEntityNotValidException(
            "In file $this->file : 
                      In property $property, Constraints are not valid !
                      Optional keys are | $optionalConstraintKeys |"
        );
    }

    /**
     * @param string $property
     * @throws YamlEntityNotValidException
     */
    protected function throwMandatoryFkKeysException(string $property)
    {
        $mandatoryFkKeys = implode(',', self::MANDATORY_FK_KEYS);
        throw new YamlEntityNotValidException(
            "In file $this->file : 
                      In property $property, Foreign Key keys are not valid !
                      Mandatory keys are | $mandatoryFkKeys |"
        );
    }


    /**
     * @param array $optionalConstraints
     * @return array
     */
    protected function getOptionalConstraintKeys($optionalConstraints)
    {
        $optionalConstraintKeys = array_keys($optionalConstraints);
        return array_diff(
            $optionalConstraintKeys,
            self::OPTIONAL_CONSTRAINTS_KEYS
        );
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
