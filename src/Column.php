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
 * Column configuration
 */
class Column
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dbField;

    /**
     * @var bool
     */
    private $searchable;

    /**
     * @var bool
     */
    private $orderable;

    /**
     * @var array
     */
    private $classes;

    /**
     * Column constructor
     *
     * @param string|null $name
     * @param string|null $dbField
     * @param bool $searchable
     * @param bool $orderable
     * @param array|null $classes
     */
    public function __construct(
        ?string $name,
        ?string $dbField,
        bool $searchable,
        bool $orderable,
        ?array $classes = []
    ) {
        $this->name = $name;
        $this->dbField = $dbField;
        $this->searchable = $searchable;
        $this->orderable = $orderable;
        $this->classes = $classes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDbField(): string
    {
        return $this->dbField;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    /**
     * @return array|null
     */
    public function getClasses(): ?array
    {
        return $this->classes;
    }
}
