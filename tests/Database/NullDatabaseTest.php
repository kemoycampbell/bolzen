<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 11:54 AM
 */

namespace Bolzen\Test\Database;

use Bolzen\Core\Config\Config;
use Bolzen\Core\Database\NullDatabase;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NullDatabaseTest extends TestCase
{
    private $config;
    private $nullDatabase;

    public function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->nullDatabase = new NullDatabase($this->config);
    }

    public function testSelectThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database operation \'select\' cannot be performed: No database plugin is enabled');
        
        $this->nullDatabase->select('table', 'columns');
    }

    public function testCreateThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database operation \'create\' cannot be performed: No database plugin is enabled');
        
        $this->nullDatabase->create('table', 'columns', []);
    }

    public function testReadThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database operation \'read\' cannot be performed: No database plugin is enabled');
        
        $this->nullDatabase->read('table', 'columns');
    }

    public function testInsertThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database operation \'insert\' cannot be performed: No database plugin is enabled');
        
        $this->nullDatabase->insert('table', 'columns', []);
    }

    public function testUpdateThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database operation \'update\' cannot be performed: No database plugin is enabled');
        
        $this->nullDatabase->update('table', 'columns', 'where', []);
    }

    public function testDeleteThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database operation \'delete\' cannot be performed: No database plugin is enabled');
        
        $this->nullDatabase->delete('table', 'where', []);
    }
}