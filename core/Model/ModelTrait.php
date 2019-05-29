<?php
/**
 * @author Kemoy Campbell
 * Date: 1/1/19
 * Time: 12:08 PM
 */

namespace Bolzen\Core\Model;

trait ModelTrait
{
    protected $errors = array();

    /**
     * This function commit the change to the database
     * @param string $error
     */
    final public function setError(string $error)
    {
        array_push($this->errors, $error);
    }

    /**
     * This function savet he change to the database
     */
    final public function save():bool
    {
        return $this->database->commit();
    }

    /**
     * This function return all the errors
     * @return array
     */
    final public function getError():array
    {
        return $this->errors;
    }

    final public function hasError():bool
    {
        return count($this->errors) > 0;
    }

    /**
     * This function returns the errors in string format
     * @return string
     */
    final public function errorToString():string
    {
        return implode(",", $this->errors);
    }
}
