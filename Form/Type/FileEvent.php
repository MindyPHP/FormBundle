<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FileEvent
{
    const CLEAR_VALUE = '__remove';

    /**
     * @var FormBuilderInterface
     */
    protected $builder;
    /**
     * @var Request
     */
    protected $request;

    /**
     * FileEvent constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    public function setFormBuilder(FormBuilderInterface $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function fetchParams(string $name): array
    {
        $bag = $this->request->request;
        return $bag->has($name) ? $bag->get($name, []) : $bag->all();
    }

    /**
     * @param FormEvent $event
     */
    public function __invoke(FormEvent $event)
    {
        $form = $event->getForm();
        $parent = $form->getParent();
        $name = $this->builder->getName();

        $params = $this->fetchParams($parent ? $parent->getName() : $form->getName());

        if (array_key_exists($name, $params)) {
            if ($params[$name] === self::CLEAR_VALUE) {
                $form->setData('');
            } elseif ($event->getData() === null) {
                $parent->remove($name);
            }
        }
    }
}
