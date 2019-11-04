<?php


namespace Bolzen\Src\Home\Model;


use Bolzen\Core\Model\Model;

class Users extends Model
{
    public function getUsers():array
    {
        $table = "users";
        $columns = "*";
        return $this->database->select($table, $columns)->fetchAll();
    }

//    public function insert($username, $password)
//    {
//        $table = "users"
//        return $this->database->insert($table, $columns, $bindings);
//    }
}