<?php
/**
 * @author Kemoy Campbell
 * Date: 1/2/19
 * Time: 6:12 PM
 */

namespace Bolzen\Src\Home\Model;

use Bolzen\Core\Model\Model;

class HomeModel extends Model
{
    public function listUsers():array
    {
        //this is equivalent to select columns from  table
        $table = "account";
        $columns = "username";
        return $this->database->select($table, $columns)->fetchAll();

    }

}