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

interface ColumnTransformerInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function transform(array $data): array;
}
