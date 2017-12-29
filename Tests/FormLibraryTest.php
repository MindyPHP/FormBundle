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

use Mindy\Bundle\FormBundle\Library\FormLibrary;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormRenderer;

class FormLibraryTest extends TestCase
{
    public function testHelpers()
    {
        $renderer = $this
            ->getMockBuilder(FormRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $library = new FormLibrary($renderer);
        $this->assertSame([
            'form_theme',
            'form_start',
            'form_end',
            'form_block',
            'form_render',
            'form_label',
            'form_errors',
            'form_row',
            'form_rest',
            'form_widget',
            'form_humanize',
        ], array_keys($library->getHelpers()));
    }
}
