<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\Form\DataTransformer;

use Mindy\Orm\QuerySetInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class QuerySetTransformer
 */
class QuerySetTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value instanceof QuerySetInterface) {
            return $value->all();
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return (array) $value;
    }
}
