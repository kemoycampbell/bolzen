<?php


namespace Bolzen\Core\Model;

trait ErrorTrait
{
    protected array $errors = array();

    final public function setError(string $error)
    {
        array_push($this->errors, $error);
    }

    /**
     * This function returns the errors in string format
     * @return string
     */
    final public function errorToString(): string
    {
        return implode(",", $this->errors);
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
}
