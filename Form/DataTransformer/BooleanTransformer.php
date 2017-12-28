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

use Symfony\Component\Form\DataTransformerInterface;

class BooleanTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return (bool) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return (int) $value;
    }
}
