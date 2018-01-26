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
use Symfony\Component\Form\Extension\Core\Type\FileType as BaseFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
    /**
     * @var FileEvent
     */
    protected $event;

    /**
     * FileType constructor.
     *
     * @param FileEvent $event
     */
    public function __construct(FileEvent $event)
    {
        $this->event = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mfile';
    }

    /**
     * @param array|object $data
     * @param string       $name
     *
     * @return null|string
     */
    public function resolveFileUrl($data, string $name)
    {
        $value = '';
        if (null !== $data) {
            if (is_array($data) && isset($data[$name])) {
                $value = $data[$name];
            } elseif (is_object($data) && property_exists($data, $name)) {
                $value = $data->{$name};
            }

            return is_string($value) ? $value : '';
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['asset_name'] = $options['asset_name'];

        $parent = $form->getParent();
        $view->vars['file_url'] = $parent ?
            $this->resolveFileUrl($parent->getData(), $form->getName()) :
            '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'asset_name' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            $this->event->setFormBuilder($builder)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return BaseFileType::class;
    }
}
