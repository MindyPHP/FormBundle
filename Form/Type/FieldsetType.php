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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldsetType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'legend' => '',
                'inherit_data' => true,
                'options' => [],
                'fields' => [],
                'label' => false,
            ])
            ->addAllowedTypes('fields', ['array', 'callable']);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['fields'])) {
            if (is_callable($options['fields'])) {
                $options['fields']($builder);
            } elseif (is_array($options['fields'])) {
                foreach ($options['fields'] as $field) {
                    $builder->add($field['name'], $field['type'], $field['attr']);
                }
            }
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (false !== $options['legend']) {
            $view->vars['legend'] = $options['legend'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fieldset';
    }
}
