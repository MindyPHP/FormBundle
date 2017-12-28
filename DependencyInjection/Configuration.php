<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder->root('form');
        $root
            ->children()
                ->arrayNode('themes')
                    ->fixXmlConfig('themes')
                    ->addDefaultChildrenIfNoneSet()
                    ->prototype('scalar')->defaultValue('default')->end()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !in_array('default', $v);
                        })
                        ->then(function ($v) {
                            return array_merge(['default'], $v);
                        })
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
