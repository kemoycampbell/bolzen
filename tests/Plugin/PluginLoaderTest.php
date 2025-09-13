<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 11:54 AM
 */

namespace Bolzen\Test\Plugin;

use Bolzen\Core\Config\Config;
use Bolzen\Core\Plugin\PluginLoader;
use Bolzen\Plugins\Database\MySQLDatabasePlugin;
use PHPUnit\Framework\TestCase;

class PluginLoaderTest extends TestCase
{
    private $config;
    private $pluginLoader;

    public function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->pluginLoader = new PluginLoader($this->config);
    }

    public function testRegisterPlugin()
    {
        $plugin = new MySQLDatabasePlugin($this->config);
        $this->pluginLoader->registerPlugin($plugin);

        $retrievedPlugin = $this->pluginLoader->getPlugin('mysql_database');
        $this->assertSame($plugin, $retrievedPlugin);
    }

    public function testGetNonExistentPlugin()
    {
        $plugin = $this->pluginLoader->getPlugin('non_existent');
        $this->assertNull($plugin);
    }

    public function testIsPluginEnabledWithDisabledPlugin()
    {
        $this->config->method('isDatabaseRequired')->willReturn(false);
        $plugin = new MySQLDatabasePlugin($this->config);
        $this->pluginLoader->registerPlugin($plugin);

        $this->assertFalse($this->pluginLoader->isPluginEnabled('mysql_database'));
    }

    public function testIsPluginEnabledWithEnabledPlugin()
    {
        $this->config->method('isDatabaseRequired')->willReturn(true);
        $plugin = new MySQLDatabasePlugin($this->config);
        $this->pluginLoader->registerPlugin($plugin);

        $this->assertTrue($this->pluginLoader->isPluginEnabled('mysql_database'));
    }
}