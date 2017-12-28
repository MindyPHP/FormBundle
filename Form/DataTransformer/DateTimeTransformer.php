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

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;

class DateTimeTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $dateTimeFormat;

    /**
     * DateTimeTransformer constructor.
     *
     * @param string $dateTimeFormat
     */
    public function __construct($dateTimeFormat = 'Y-m-d H:i:s')
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value instanceof DateTime) {
            return $value;
        }

        return new DateTime($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value instanceof DateTime) {
            return $value->format($this->dateTimeFormat);
        }

        return (string) $value;
    }
}
