<?php


namespace Bolzen\Core\Filter;

class Filter implements FilterInterface
{

    private string $where;
    private array $bindings;

    public function __construct(string $where, array $bindings)
    {
        $this->bindings = $bindings;
        $this->where = $where;
    }

    /**
     * @inheritDoc
     */
    public function where(): string
    {
        return $this->where;
    }

    /**
     * @inheritDoc
     */
    public function bindings(): array
    {
        return $this->bindings;
    }
}