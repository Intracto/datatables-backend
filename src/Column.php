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
     * Column constructor
     *
     * @param string $name
     * @param string $dbField
     * @param bool $searchable
     * @param bool $orderable
     */
    public function __construct(
        $name,
        $dbField,
        $searchable,
        $orderable
    ) {
        $this->name = $name;
        $this->dbField = $dbField;
        $this->searchable = $searchable;
        $this->orderable = $orderable;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDbField()
    {
        return $this->dbField;
    }

    /**
     * @return bool
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function isOrderable()
    {
        return $this->orderable;
    }
}
