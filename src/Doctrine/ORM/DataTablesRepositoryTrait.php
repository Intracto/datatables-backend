<?php

/*
 * This file is part of the Intracto datatables-backend package.
 *
 * (c) Intracto <http://www.intracto.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Intracto\DataTables\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Intracto\DataTables\Columns;
use Intracto\DataTables\Parameters;

/**
 * Implementation of necessary methods for the DataTables Provider
 */
trait DataTablesRepositoryTrait
{
    /**
     * {@inheritdoc}
     */
    public function getDataTablesTotalRecordsCount(Parameters $parameters, Columns $columns): int
    {
        return $this->createQueryBuilder(self::ENTITY_ALIAS)
            ->select('count(' . self::ENTITY_ALIAS . ')')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataTablesFilteredRecordsCount(Parameters $parameters, Columns $columns): int
    {
        $qb = $this->getFilteredDataTablesQb($parameters, $columns);

        return $qb->select('count(' . self::ENTITY_ALIAS . ')')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataTablesData(Parameters $parameters, Columns $columns): array
    {
        $qb = $this->getFilteredDataTablesQb($parameters, $columns);
        $qb->setFirstResult($parameters->getStart())
            ->setMaxResults($parameters->getLength());

        if ($parameters->getOrderField() !== null) {
            $qb->orderBy(
                self::ENTITY_ALIAS . '.' . $parameters->getOrderField(),
                strtolower($parameters->getOrderDirection()) === Parameters::ORDER_DIRECTION_ASC ? Parameters::ORDER_DIRECTION_ASC : Parameters::ORDER_DIRECTION_DESC
            );
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     *
     * @return QueryBuilder
     */
    private function getFilteredDataTablesQb(Parameters $parameters, Columns $columns)
    {
        $qb = $this->createQueryBuilder(self::ENTITY_ALIAS);

        $this->addFilters($parameters, $qb);

        $this->addSearch($parameters, $columns, $qb);

        return $qb;
    }

    /**
     * @param Parameters $parameters
     * @param QueryBuilder $qb
     *
     * @return void
     */
    private function addFilters(Parameters $parameters, QueryBuilder $qb)
    {
        if ($parameters->hasFilters() === false) {
            return;
        }

        $expression = $qb->expr();
        $parts = array();

        foreach ($parameters->getFilters() as $field => $value) {
            if (empty($value)) {
                continue;
            }

            if (is_callable($value)) {
                $parts[] = $value($qb);
            } else {
                $parts[] = $qb->expr()->eq(
                    self::ENTITY_ALIAS . '.' . $field,
                    ':'.$field
                );
                $qb->setParameter($field, $value);
            }
        }

        if (count($parts) === 0) {
            return;
        }

        $qb->andWhere(
            call_user_func_array(
                array(
                    $expression,
                    'andX',
                ),
                $parts
            )
        );
    }

    /**
     * @param Parameters $parameters
     * @param Columns $columns
     * @param QueryBuilder $qb
     *
     * @return void
     */
    private function addSearch(Parameters $parameters, Columns $columns, QueryBuilder $qb)
    {
        if ($parameters->hasSearchString() === false) {
            return;
        }

        if (0 === count($columns->getSearchableFields())) {
            return;
        }
        
        $searchString = $parameters->getSearchString();

        $expression = $qb->expr();

        $parts = array();
        foreach ($columns->getSearchableFields() as $field) {
            $parts[] = $qb->expr()->like(
                self::ENTITY_ALIAS . '.' . $field,
                $qb->expr()->literal(
                    '%' . $searchString . '%'
                )
            );
        }

        $qb->andWhere(
            call_user_func_array(
                array(
                    $expression,
                    'orX',
                ),
                $parts
            )
        );
    }
}
