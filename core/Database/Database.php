<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 7:58 PM
 */

namespace Bolzen\Core\Database;

use Bolzen\Core\Config\ConfigInterface;

class Database implements DatabaseInterface
{

    private $pdo;
    private $config;
    private $autoCommit;
    private $pendingTransaction;


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->autoCommit = false;
        $this->pendingTransaction = false;
        if ($this->config->isDatabaseRequired()) {
            $this->pdo = $this->connect();
        }
    }

    private function connect()
    {
        $conn = new \PDO(
            $this->config->databaseDsn(),
            $this->config->databaseUser(),
            $this->config->databasePassword()
        );

        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        return $conn;
    }

    /**
     * Take a given sql and execute the statement
     * @param string $sql the given sql statement to execute
     * @param array $bindings the associate bindings if any. This must be in the same order as the sql placeholders
     * @return \PDOStatement returns a PDOStatement object after the sql has been executed
     */
    public function genericSqlBuilder(string $sql, array $bindings = array()): \PDOStatement
    {
        /*
         * The transaction is only started if autocommit is not set to true
         * and there are no pending transaction. This logic is already
         * defined in the beginTransaction method
         */
        $this->beginTransaction();
        //$sql = htmlentities($sql, ENT_QUOTES, "UTF-8");



        /*
         * set up the statement using the prepare statement
         * execute it and return the PDOStatement obj
         */
        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        return $statement;
    }

    /**
     * Returns the PDO instance of the database
     * @return \PDO the PDO instance of the database
     */
    public function getPDO(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Perform a select statement on a given table based on the values supplied
     * @param string $table the table to perform the sql on
     * @param string $columns the columns to select
     * @param string $where the where clause example "id=?"
     * @param array $bindings the bindings for the where clause(s)
     * @return \PDOStatement return a PDOStatement after the sql has been executed
     */
    public function select(string $table, string $columns, string $where = "", array $bindings = array()): \PDOStatement
    {
        $sql = "SELECT $columns FROM $table ";

        //append the where statement if need
        if (!empty($where)) {
            $sql.="WHERE $where";
        }

        return $this->genericSqlBuilder($sql, $bindings);
    }

    /**
     * Perform an insert sql statement
     * @param string $table the table to insert into
     * @param string $columns the columns to insert into
     * @param array $bindings the parameterized data to insert into the columns.
     *                        must be in the same order as the column
     * @return bool true if successful. False otherwise
     */
    public function insert(string $table, string $columns, array $bindings): bool
    {
        //by default we will set values to just and append more if need
        $values = "?";

        //try parsing the , from the column(s)
        $fields = explode(",", $columns);

        $count = count($fields);
        if ($count > 1) {
            //append ? for the rest of the columns not including the first one
            $values.=str_repeat(",?", $count-1);
        }

        $sql = "INSERT INTO $table ($columns) VALUES($values)";

        //return a boolean on whether the insert was successful based on row(s) affected
        return $this->genericSqlBuilder($sql, $bindings)->rowCount() > 0;
    }

    /**
     * Perform update sql statement
     * @param string $table the table to update
     * @param string $columns the column to set example column1,column2,column3
     * @param string $where the where clause on which value to update
     * @param array $bindings the bindings for the parameterized values
     * @return bool true if success. False otherwise
     */
    public function update(string $table, string $columns, string $where, array $bindings): bool
    {
        $sql = "UPDATE $table SET ";
        $set = "";

        //loop through each columns and append the set
        foreach (explode(",", $columns) as $col) {
            $set.= $col." = ?,";
        }

        //remove the last,
        $set = rtrim($set, ",");

        //finish the rest of the sql statement
        $sql.="$set WHERE $where";

        return $this->genericSqlBuilder($sql, $bindings)->rowCount() > 0;
    }

    /**
     * Perform delete statement
     * @param string $table the table to delete from
     * @param string $where the where clause for the delete
     * @param array $bindings the bindings for the parameterized values for the where clause
     * @return bool true if success. False otherwise
     */
    public function delete(string $table, string $where, array $bindings): bool
    {
        $sql = "DELETE FROM $table WHERE $where";

        return $this->genericSqlBuilder($sql, $bindings)->rowCount() > 0;
    }

    /**
     * Commit the change to the database
     * @return bool true if success. False otherwise
     */
    public function commit(): bool
    {
        /*
         * We will only commit if there are any pending transaction
         */
        if ($this->pendingTransaction) {
            $this->pendingTransaction = false;
            return $this->pdo->commit();
        }

        return false;
    }

    /**
     * Return the database to the previous state. This is only
     * possible if the change has not been commit
     * @return bool true if success. False otherwise
     */
    public function rollBack(): bool
    {
        if ($this->pendingTransaction) {
            $this->pendingTransaction = false;
            return $this->pdo->rollBack();
        }

        return false;
    }

    /**
     * Set the database transaction
     */
    public function beginTransaction(): void
    {
        /*
         * If the autocommit is not set to true and there are no
         * pending transaction then we can enable it
         */
        if (!$this->autoCommit && !$this->pendingTransaction) {
            $this->pdo->beginTransaction();
            $this->pendingTransaction = true;
        }
    }

    /**
     * Set the database to autocommit and turn off transaction
     * @param bool $status true to enable autocommit. Default is false
     */
    public function setAutoCommit(bool $status = false): void
    {
        $this->autoCommit = $status;
    }

    /**
     * Return whether autocommit is enabled or not.
     * @return bool true if autocommit is enabled. False otherwise
     */
    public function isAutoCommit(): bool
    {
        return $this->autoCommit;
    }

    /**
     * Returns the name of the current connected database
     * @return string the name of the connected database
     */
    public function getDatabaseName(): string
    {
        return $this->config->databaseName();
    }
}
