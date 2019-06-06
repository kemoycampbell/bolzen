<?php
/**
 * @author Kemoy Campbell
 * Date: 12/27/18
 * Time: 11:54 AM
 */

namespace Bolzen\Core\Database;

interface DatabaseInterface
{
    /**
     * Take a given sql and execute the statement
     * @param string $sql the given sql statement to execute
     * @param array $bindings the associate bindings if any. This must be in the same order as the sql placeholders
     * @return \PDOStatement returns a PDOStatement object after the sql has been executed
     */
    public function genericSqlBuilder(string $sql, array $bindings = array()):\PDOStatement;

    /**
     * Returns the PDO instance of the database
     * @return \PDO the PDO instance of the database
     */
    public function getPDO():\PDO;

    /**
     * Perform a select statement on a given table based on the values supplied
     * @param string $table the table to perform the sql on
     * @param string $columns the columns to select
     * @param string $where the where clause example "id=?"
     * @param array $bindings the bindings for the where clause(s)
     * @return \PDOStatement return a PDOStatement after the sql has been executed
     */
    public function select(string $table, string $columns, string $where = "", array $bindings = array()):\PDOStatement;

    /**
     * Perform an insert sql statement
     * @param string $table the table to insert into
     * @param string $columns the columns to insert into
     * @param array $bindings the parameterized data to insert into the columns.
     *                        must be in the same order as the column
     * @return bool true if successful. False otherwise
     */
    public function insert(string $table, string $columns, array $bindings):bool;

    /**
     * Perform update sql statement
     * @param string $table the table to update
     * @param string $columns the column to set example column1,column2,column3
     * @param string $where the where clause on which value to update
     * @param array $bindings the bindings for the parameterized values
     * @return bool true if success. False otherwise
     */
    public function update(string $table, string $columns, string $where, array $bindings):bool;

    /**
     * Perform delete statement
     * @param string $table the table to delete from
     * @param string $where the where clause for the delete
     * @param array $bindings  the bindings for the parameterized values for the where clause
     * @return bool true if success. False otherwise
     */
    public function delete(string $table, string $where, array $bindings):bool;

    /**
     * Commit the change to the database
     * @return bool true if success. False otherwise
     */
    public function commit():bool;

    /**
     * Return the database to the previous state. This is only
     * possible if the change has not been commit
     * @return bool true if success. False otherwise
     */
    public function rollBack():bool;

    /**
     * Set the database transaction
     */
    public function beginTransaction():void;

    /**
     * Set the database to autocommit and turn off transaction
     * @param bool $status true to enable autocommit. Default is false
     */
    public function setAutoCommit(bool $status = false):void;

    /**
     * Return whether autocommit is enabled or not.
     * @return bool true if autocommit is enabled. False otherwise
     */
    public function isAutoCommit():bool;

    /**
     * Returns the name of the current connected database
     * @return string the name of the connected database
     */
    public function getDatabaseName():string;
}
