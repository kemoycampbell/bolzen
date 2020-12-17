<?php


namespace Bolzen\Core\Column;

class Column implements ColumnInterface
{
    private string $columns;
    private array $bindings;

    public function __construct(string $columns, array $bindings)
    {
        $this->bindings = $bindings;
        $this->columns = $columns;
    }

    /**
     * @inheritDoc
     */
    public function columns(): string
    {
        return $this->columns;
    }

    /**
     * @inheritDoc
     */
    public function bindings(): array
    {
        return $this->bindings;
    }
}
