<?php


namespace Bolzen\Core\Filter;


interface FilterInterface
{
    /**
     * @return string
     */
    public function where():string;

    /**
     * @return array
     */
    public function bindings():array;
}
