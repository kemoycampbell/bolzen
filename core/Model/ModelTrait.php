<?php
/**
 * @author Kemoy Campbell
 * Date: 1/1/19
 * Time: 12:08 PM
 */

namespace Bolzen\Core\Model;

trait ModelTrait
{
    /**
     * This function commit the change to the database
     */
    final public function save():bool
    {
        return $this->database->commit();
    }
}
