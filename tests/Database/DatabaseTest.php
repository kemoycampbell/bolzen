<?php
/**
 * @author Kemoy Campbell
 * Date: 12/28/18
 * Time: 4:12 PM
 */
namespace Bolzen\Test\Database;

use Bolzen\Core\Config\Config;
use Bolzen\Core\Database\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $config;
    private $database;
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = new Config();
        $this->database = new Database($this->config);
    }

    public function testRollBack()
    {

    }

    public function testBeginTransaction()
    {

    }

    public function testIsAutoCommit()
    {
        self::assertEquals(false, $this->database->isAutoCommit());
    }

    public function testInsert()
    {
        $res = $this->database->insert("account", "username", array("ksc2650"));
        $this->database->commit();
        self::assertEquals(true, $res);
    }

    public function testDelete()
    {
        $res = $this->database->delete("account", "username = ?", array("ksc2651"));
        $this->database->commit();
        self::assertEquals(true, $res);
    }


    public function testCommit()
    {

    }



    public function testUpdate()
    {
        $res = $this->database->update("account", "username", "username = ?", array("ksc2651","ksc2650"));
        $this->database->commit();
        self::assertEquals(true, $res);
    }

    public function testSetAutoCommit()
    {

    }

    public function testSelect()
    {
    }

    public function testGetDatabaseName()
    {
        self::assertEquals("bolzen", $this->database->getDatabaseName());
    }

    public function testGenericSqlBuilder()
    {

    }
}
