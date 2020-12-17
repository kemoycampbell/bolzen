<?php


namespace Bolzen\Core\Column;


interface ColumnInterface
{
    /**
     * @return string
     */
    public function columns():string;

    /**
     * @return array
     */
    public function bindings():array;
}