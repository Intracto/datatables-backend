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
 * Defines the public API of a DataTablesRepository
 */
interface DataTablesRepositoryInterface
{
    const ENTITY_ALIAS = 'x';

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     *
     * @return int
     */
    public function getDataTablesTotalRecordsCount(Parameters $parameters, Columns $columns);

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     *
     * @return int
     */
    public function getDataTablesFilteredRecordsCount(Parameters $parameters, Columns $columns);

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     *
     * @return array
     */
    public function getDataTablesData(Parameters $parameters, Columns $columns);
}
