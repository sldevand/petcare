<?php

namespace Tests\Integration\Db\Pdo\Query;

use Exception;
use Framework\Db\Pdo\Query\Constraint;

/**
 * Class ConstraintTest
 * @package Tests\Integration\Db\Pdo\Query
 */
class ConstraintTest extends BaseQueryTest
{
    /**
     * @throws Exception
     */
    public function testConstraintHydrate()
    {
        $data = self::$mocks->getConstraintData();

        $expectedConstraint = new Constraint();
        $expectedConstraint->setName($data['name']);

        $constraint = new Constraint($data);

        $this->assertEquals($constraint, $expectedConstraint);
    }
}
