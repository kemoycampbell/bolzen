<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 7:58 PM
 */

namespace Bolzen\Core\Database;

use Bolzen\Core\Config\ConfigInterface;
use PDO;
use PDOStatement;
use RuntimeException;

/**
 * Null implementation of DatabaseInterface for applications that don't require database functionality
 * Provides meaningful error messages when database operations are attempted without a database plugin
 */
class NullDatabase implements DatabaseInterface
{
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    private function throwDatabaseNotAvailableException(string $operation): void
    {
        throw new RuntimeException(
            "Database operation '$operation' cannot be performed: No database plugin is enabled. " .
            "To use database functionality, enable a database plugin in your configuration " .
            "(set 'enable_database: true' in config.yaml and configure database connection settings)."
        );
    }

    public function genericSqlBuilder(string $sql, array $bindings = array()): PDOStatement
    {
        $this->throwDatabaseNotAvailableException('genericSqlBuilder');
    }

    public function getPDO(): PDO
    {
        $this->throwDatabaseNotAvailableException('getPDO');
    }

    public function select(string $table, string $columns, string $where = "", array $bindings = array()): PDOStatement
    {
        $this->throwDatabaseNotAvailableException('select');
    }

    public function create(string $table, string $columns, array $bindings): bool
    {
        $this->throwDatabaseNotAvailableException('create');
    }

    public function read(string $table, string $columns, string $where = "", array $bindings = array()): PDOStatement
    {
        $this->throwDatabaseNotAvailableException('read');
    }

    public function insert(string $table, string $columns, array $bindings): bool
    {
        $this->throwDatabaseNotAvailableException('insert');
    }

    public function update(string $table, string $columns, string $where, array $bindings): bool
    {
        $this->throwDatabaseNotAvailableException('update');
    }

    public function delete(string $table, string $where, array $bindings): bool
    {
        $this->throwDatabaseNotAvailableException('delete');
    }

    public function commit(): bool
    {
        $this->throwDatabaseNotAvailableException('commit');
    }

    public function rollBack(): bool
    {
        $this->throwDatabaseNotAvailableException('rollBack');
    }

    public function beginTransaction(): void
    {
        $this->throwDatabaseNotAvailableException('beginTransaction');
    }

    public function setAutoCommit(bool $status = false): void
    {
        $this->throwDatabaseNotAvailableException('setAutoCommit');
    }

    public function isAutoCommit(): bool
    {
        $this->throwDatabaseNotAvailableException('isAutoCommit');
    }

    public function getDatabaseName(): string
    {
        $this->throwDatabaseNotAvailableException('getDatabaseName');
    }
}