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
 * Get data for DataTables AJAX calls
 */
class DataProvider
{
    /**
     * @param Parameters $parameters
     * @param Columns $columns
     * @param DataTablesRepositoryInterface $dataTablesRepository
     * @param ColumnTransformerInterface $columnTransformer
     *
     * @return array
     */
    public function getData(
        Parameters $parameters,
        Columns $columns,
        DataTablesRepositoryInterface $dataTablesRepository,
        ColumnTransformerInterface $columnTransformer
    ): array {
        return array(
            'draw' => $parameters->getDraw(),
            'recordsTotal' => $dataTablesRepository->getDataTablesTotalRecordsCount($parameters, $columns),
            'recordsFiltered' => $dataTablesRepository->getDataTablesFilteredRecordsCount($parameters, $columns),
            'data' => $this->getColumns($parameters, $columns, $dataTablesRepository, $columnTransformer),
        );
    }

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     * @param DataTablesRepositoryInterface $dataTablesRepository
     * @param ColumnTransformerInterface $columnTransformer
     *
     * @return array
     */
    private function getColumns(
        Parameters $parameters,
        Columns $columns,
        DataTablesRepositoryInterface $dataTablesRepository,
        ColumnTransformerInterface $columnTransformer
    ): array {
        $data = $dataTablesRepository->getDataTablesData($parameters, $columns);

        return $columnTransformer->transform($data);
    }
}
