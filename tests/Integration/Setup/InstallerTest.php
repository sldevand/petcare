<?php

namespace Tests\Integration\Setup;

use PHPUnit\Framework\TestCase;
use Tests\Integration\Framework\BaseTestFramework;

/**
 * Class InstallerTest
 * @package Tests\Integration\Setup
 */
class InstallerTest extends TestCase
{
    public function testExecute()
    {
        $app = BaseTestFramework::generateApp();
        $installer = $app->getContainer()->get('installerTest');
        $installer->execute();
        $installer->execute();
    }
}
