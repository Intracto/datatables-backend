<?php

/*
 * This file is part of the Intracto datatables-backend package.
 *
 * (c) Intracto <http://www.intracto.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intracto\DataTables\Doctrine\ODM;

use Doctrine\ODM\MongoDB\Query\Builder;
use Intracto\DataTables\Columns;
use Intracto\DataTables\Parameters;
use MongoDB\BSON\Regex;

trait DataTablesRepositoryTrait
{
    /**
     * {@inheritdoc}
     */
    public function getDataTablesTotalRecordsCount(Parameters $parameters, Columns $columns)
    {
        return iterator_count($this->createQueryBuilder()->getQuery()->execute());
    }

    /**
     * {@inheritdoc}
     */
    public function getDataTablesFilteredRecordsCount(Parameters $parameters, Columns $columns)
    {
        return iterator_count($this->getFilteredDataTablesQb($parameters, $columns)->getQuery()->execute());
    }

    /**
     * {@inheritdoc}
     */
    public function getDataTablesData(Parameters $parameters, Columns $columns)
    {
        $qb = $this->getFilteredDataTablesQb($parameters, $columns);
        $qb->limit($parameters->getLength())->skip($parameters->getStart());

        if ($parameters->getOrderField() !== null) {
            $qb->sort($parameters->getOrderField(), strtolower($parameters->getOrderDirection()) === Parameters::ORDER_DIRECTION_ASC ? 1 : -1);
        }

        return $qb->getQuery()->execute()->toArray(false);
    }

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     *
     * @return Builder
     */
    private function getFilteredDataTablesQb(Parameters $parameters, Columns $columns)
    {
        $qb = $this->createQueryBuilder();

        $this->addFilters($parameters, $qb);

        $this->addSearch($parameters, $columns, $qb);

        return $qb;
    }

    /**
     * @param Parameters $parameters
     * @param Builder $qb
     *
     * @return void
     */
    private function addFilters(Parameters $parameters, Builder $qb)
    {
        if ($parameters->hasFilters() === false) {
            return;
        }

        foreach ($parameters->getFilters() as $field => $value) {
            if (empty($value)) {
                continue;
            }

            if (is_callable($value)) {
                $value($qb);
            } else {
                $qb->field($field)->equals(new Regex($value, 'i'));
            }
        }
    }

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     * @param Builder $qb
     *
     * @return void
     */
    private function addSearch(Parameters $parameters, Columns $columns, Builder $qb)
    {
        if ($parameters->hasSearchString() === false) {
            return;
        }

        $searchString = $parameters->getSearchString();

        $expression = $qb->expr();

        foreach ($columns->getSearchableFields() as $field) {
            $searchExpr = $qb->expr()->field($field)->equals(new Regex($searchString, 'i'));
            $expression->addOr($searchExpr);
        }

        $qb->addAnd($expression);
    }
}
