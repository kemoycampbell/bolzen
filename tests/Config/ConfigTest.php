<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 11:06 AM
 */

namespace Bolzen\Test\Config;

use Bolzen\Core\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private $config;
    private $dbName;
    private $dbHost;
    private $dbUser;
    private $dbPrefix;
    private $dbPass;
    private $scheme;
    private $directory;
    private $requireDatabase;
    private $environment;
    private $serverHost;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = new Config();
        $this->dbName = "bolzen";
        $this->dbHost = "localhost";
        $this->dbUser = "root";
        $this->dbPrefix = "mysql";
        $this->dbPass = "team22#1!";
        $this->scheme = "http";
        $this->directory = "huston";
        $this->requireDatabase = true;
        $this->environment = "dev";
        $this->serverHost = "localhost";
    }


    public function testDatabaseName()
    {
        self::assertEquals($this->dbName, $this->config->databaseName());
    }

    public function testIsDebugEnabled()
    {
        self::assertEquals(true, $this->config->isDebugEnabled());
    }

    public function testDatabaseDsn()
    {
        $dsn = $this->dbPrefix.":host=".$this->dbHost.";dbname=".$this->dbName.";charset=utf8mb4";
        self::assertEquals($dsn, $this->config->databaseDsn());
    }

    public function testDatabaseHost()
    {
        self::assertEquals($this->dbHost, $this->config->databaseHost());
    }

    public function testScheme()
    {
        self::assertEquals($this->scheme, $this->config->scheme());
    }

    public function testDatabasePassword()
    {
        self::assertEquals($this->dbPass, $this->config->databasePassword());
    }

    public function testProjectDirectory()
    {
        self::assertEquals($this->directory, $this->config->projectDirectory());
    }

    public function testDatabasePrefix()
    {
        self::assertEquals($this->dbPrefix, $this->config->databasePrefix());
    }

    public function testIsDatabaseRequired()
    {
        self::assertEquals($this->requireDatabase, $this->config->isDatabaseRequired());
    }

    public function testDatabaseUser()
    {
        self::assertEquals($this->dbUser, $this->config->databaseUser());
    }

    public function testGetBaseUrl()
    {
        $baseUrl = $this->scheme."://".$this->serverHost."/".$this->directory."/";
        self::assertEquals($baseUrl, $this->config->getBaseUrl());
    }

    public function testServerHost()
    {
        self::assertEquals($this->serverHost, $this->config->serverHost());
    }

    public function testEnvironment()
    {
        self::assertEquals($this->environment, $this->config->environment());
    }
}
