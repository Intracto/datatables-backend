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

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class to hold database parameters
 */
class Parameters
{
    const ORDER_DIRECTION_ASC = 'asc';
    const ORDER_DIRECTION_DESC = 'desc';

    /**
     * @var string
     */
    private $draw;

    /**
     * @var string
     */
    private $search;

    /**
     * @var string
     */
    private $start;

    /**
     * @var string
     */
    private $length;

    /**
     * @var string
     */
    private $order;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var Columns
     */
    private $columns;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Parameters constructor
     */
    private function __construct()
    {
    }

    /**
     * @param ParameterBag $parameterBag
     * @param Columns $columns
     * @param array $attributes
     *
     * @return Parameters
     */
    public static function fromParameterBag(ParameterBag $parameterBag, Columns $columns, array $attributes = array())
    {
        $parameters = new Parameters();

        $parameters->draw = $parameterBag->get('draw');
        $parameters->search = $parameterBag->all('search');
        $parameters->start = $parameterBag->get('start');
        $parameters->length = $parameterBag->get('length');
        $parameters->order = $parameterBag->all('order');
        $parameters->filters = $parameterBag->all('filters');
        $parameters->columns = $columns;
        $parameters->attributes = $attributes;

        return $parameters;
    }

    /**
     * @return int
     */
    public function getDraw()
    {
        return $this->draw;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return (int) $this->length;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return (int) $this->start;
    }

    /**
     * @return string
     */
    public function getOrderField()
    {
        // Only sorting on one column supported for now
        $columnIndex = $this->order[0]['column'];

        if ($this->columns->offsetExists($columnIndex) === false) {
            return null;
        }

        return $this->columns->offsetGet($columnIndex)->getDbField();
    }

    /**
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->order[0]['dir']; // Only sorting on one column supported for now
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return bool
     */
    public function hasFilters()
    {
        if (null === $this->filters) {
            return false;
        }

        return count($this->filters) > 0;
    }

    /**
     * Adds a filter to the parameters
     *
     * @param string|callable $field
     * @param mixed|null $value
     *
     * @return void
     */
    public function addFilter($field, $value = null)
    {
        if (is_callable($field)) {
            return $this->addCallableFilter($field);
        }

        $this->filters[$field] = $value;
    }

    /**
     * Adds a callable to the filters
     *
     * @param callable $callback
     *
     * @return void
     */
    public function addCallableFilter(callable $callback)
    {
        $this->filters['callable_' . count($this->filters)] = $callback;
    }

    /**
     * @return string
     */
    public function hasSearchString()
    {
        return empty($this->search['value']) === false;
    }

    /**
     * @return string
     */
    public function getSearchString()
    {
        return $this->search['value'];
    }

    /**
     * Returns a parameter by name.
     *
     * @param string $key The key
     * @param mixed  $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : $default;
    }
}
