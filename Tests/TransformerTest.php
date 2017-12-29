<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\Tests;

use DateTime;
use Mindy\Bundle\FormBundle\Form\DataTransformer\BooleanTransformer;
use Mindy\Bundle\FormBundle\Form\DataTransformer\DateTimeTransformer;
use Mindy\Bundle\FormBundle\Form\DataTransformer\QuerySetTransformer;
use Mindy\Orm\QuerySetInterface;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public function testQuerySetTransformer()
    {
        $transformer = new QuerySetTransformer();

        $qs = $this
            ->getMockBuilder(QuerySetInterface::class)
            ->getMock();
        $qs->method('all')->willReturn([]);

        $this->assertSame([], $transformer->transform([]));
        $this->assertSame([], $transformer->transform($qs));
        $this->assertSame([123], $transformer->reverseTransform(123));
    }

    public function testDateTimeTransformer()
    {
        $transformer = new DateTimeTransformer();
        $this->assertInstanceOf(DateTime::class, $transformer->transform(''));
        $this->assertInstanceOf(DateTime::class, $transformer->transform(new DateTime()));
        $this->assertSame(date('Y-m-d H:i:s'), $transformer->reverseTransform(new DateTime()));
        $this->assertSame('', $transformer->reverseTransform(''));
    }

    public function testBooleanTransformer()
    {
        $transformer = new BooleanTransformer();
        $this->assertTrue($transformer->transform('1'));
        $this->assertSame(0, $transformer->reverseTransform(''));
    }
}
