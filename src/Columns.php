<?php

/*
 * This file is part of the Intracto datatables-backend package.
 *
 * (c) Intracto <http://www.intracto.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intracto\DataTables;

/**
 * Container class to hold Column objects
 */
abstract class Columns implements \ArrayAccess, \Iterator
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var int
     */
    private $position;

    /**
     * Columns constructor
     *
     * @param Column[] $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->columns);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): mixed
    {
        if ($this->offsetExists($offset) === false) {
            throw new \InvalidArgumentException(sprintf('%s column not available', $offset));
        }

        return $this->columns[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        if ($this->offsetExists($offset) === false) {
            $this->columns[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset) === false) {
            unset($this->columns[$offset]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function current(): mixed
    {
        return $this->columns[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function key(): mixed
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return array_key_exists($this->position, $this->columns);
    }

    /**
     * @return array
     */
    public function getSearchableFields(): array
    {
        $searchable = array();

        foreach ($this->columns as $column) {
            if ($column->isSearchable()) {
                $searchable[] = $column->getDbField();
            }
        }

        return $searchable;
    }
}
