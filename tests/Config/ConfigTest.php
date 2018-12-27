<?php
/**
 * @author Kemoy Campbell
 * Date: 12/26/18
 * Time: 6:59 PM
 */

namespace Bolzen\Test\Config;

use Bolzen\Core\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private $config;

    protected function setUp()
    {
        $this->config = new Config();
    }

    protected function tearDown()
    {
        unset($this->config);
    }

    public function testDatabasePrefix()
    {

    }

    public function testProjectDirectory()
    {

    }

    public function testDatabaseName()
    {

    }

    public function testGetBaseUrl()
    {

    }

    public function testDatabaseUser()
    {

    }

    public function testDatabaseHost()
    {

    }

    public function testScheme()
    {

    }

    public function testServerHost()
    {

    }

    public function testEnvironment()
    {

    }

    public function testIsDatabaseRequired()
    {

    }

    public function testDatabaseDsn()
    {

    }

    public function testDatabasePassword()
    {

    }

    public function testIsDebugEnabled()
    {

    }
}
