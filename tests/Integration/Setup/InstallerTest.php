<?php

namespace Tests\Integration\Setup;

use PHPUnit\Framework\TestCase;
use Slim\App;

/**
 * Class InstallerTest
 * @package Tests\Integration\Setup
 */
class InstallerTest extends TestCase
{
    public function testExecute()
    {
        require_once __DIR__ . '/../../../src/bootstrap.php';
        require_once VENDOR_DIR . '/autoload.php';
        $settings = require SRC_DIR . '/settings.php';
        $app = new App($settings);
        require_once SRC_DIR . '/dependencies.php';

        $installer = $app->getContainer()->get('installer');
        $installer->execute();
    }
}