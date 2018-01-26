<?php

declare(strict_types=1);

/*
 * Studio 107 (c) 2018 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'inherit_data' => true,
                'options' => [],
                'buttons' => [],
                'label' => false,
            ])
            ->setRequired('buttons')
            ->addAllowedTypes('buttons', ['array', 'callable']);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['buttons'])) {
            if (is_callable($options['buttons'])) {
                $options['buttons']($builder);
            } elseif (is_array($options['buttons'])) {
                foreach ($options['buttons'] as $field) {
                    $builder->add($field['name'], $field['type'], $field['attr']);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'buttons';
    }
}
