# Form Bundle

[![Build Status](https://travis-ci.org/MindyPHP/FormBundle.svg?branch=master)](https://travis-ci.org/MindyPHP/FormBundle)
[![codecov](https://codecov.io/gh/MindyPHP/FormBundle/branch/master/graph/badge.svg)](https://codecov.io/gh/MindyPHP/FormBundle)
[![Latest Stable Version](https://poser.pugx.org/mindy/form-bundle/v/stable.svg)](https://packagist.org/packages/mindy/form-bundle)
[![Total Downloads](https://poser.pugx.org/mindy/form-bundle/downloads.svg)](https://packagist.org/packages/mindy/form-bundle)

The Form Bundle

Resources
---------

  * [Documentation](https://mindy-cms.com/doc/current/bundles/form/index.html)
  * [Contributing](https://mindy-cms.com/doc/current/contributing/index.html)
  * [Report issues](https://github.com/MindyPHP/mindy/issues) and
    [send Pull Requests](https://github.com/MindyPHP/mindy/pulls)
    in the [main Mindy repository](https://github.com/MindyPHP/mindy)

### FieldsetType

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $instance = $builder->getData();

        $builder
            ->add('fieldset_1', FieldsetType::class, [
                'legend' => 'Информация',
                'fields' => function (FormBuilderInterface $builder) use ($instance) {
                    $builder
                        ->add('parent', ChoiceType::class, [
                            'label' => 'Родительская категория',
                            'required' => false,
                            'choices' => Category::objects()->order(['root', 'lft'])->all(),
                            'choice_label' => function ($page) {
                                return sprintf('%s %s', str_repeat('-', $page->level - 1), $page);
                            },
                            'choice_value' => 'id',
                            'choice_attr' => function ($page) use ($instance) {
                                return $page->pk == $instance->pk ? ['disabled' => 'disabled'] : [];
                            },
                        ])
                        ->add('name', TextType::class, [
                            'label' => 'Название',
                        ])
                        ->add('image', FileType::class, [
                            'label' => 'Изображение',
                            'required' => false,
                            'constraints' => [
                                new Assert\File([
                                ]),
                            ],
                        ])
                        ->add('show_category', CheckboxType::class, [
                            'label' => 'Отображать дочерние категории (Если выключено, то отображаются только товары)',
                            'required' => false,
                        ])
                        ->add('seo', SeoFormType::class, [
                            'label' => 'Мета информация',
                            'source' => $instance,
                            'mapped' => false,
                        ]);
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
            ])
            ->add('submit_create', SubmitType::class, [
                'label' => 'Сохранить и создать',
            ]);
    }
```

### TabsType & TabType

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $instance = $builder->getData();

        $builder
            ->add('tabs', TabsType::class, [
                'tabs' => function (FormBuilderInterface $builder) use ($instance) {
                    $builder
                        ->add('first', TabType::class, [
                            'tab' => 'Информация',
                            'fields' => function (FormBuilderInterface $builder) use ($instance) {
                                $builder
                                    ->add('parent', ChoiceType::class, [
                                        'label' => 'Родительская категория',
                                        'required' => false,
                                        'choices' => Category::objects()->order(['root', 'lft'])->all(),
                                        'choice_label' => function ($page) {
                                            return sprintf('%s %s', str_repeat('-', $page->level - 1), $page);
                                        },
                                        'choice_value' => 'id',
                                        'choice_attr' => function ($page) use ($instance) {
                                            return $page->pk == $instance->pk ? ['disabled' => 'disabled'] : [];
                                        },
                                    ])
                                    ->add('name', TextType::class, [
                                        'label' => 'Название',
                                    ])
                                    ->add('image', FileType::class, [
                                        'label' => 'Изображение',
                                        'required' => false,
                                        'constraints' => [
                                            new Assert\File([
                                            ]),
                                        ],
                                    ])
                                    ->add('show_category', CheckboxType::class, [
                                        'label' => 'Отображать дочерние категории (Если выключено, то отображаются только товары)',
                                        'required' => false,
                                    ]);
                            }
                        ])
                        ->add('seo', TabType::class, [
                            'tab' => 'Мета информация',
                            'fields' => function (FormBuilderInterface $builder) use ($instance) {
                                $builder
                                    ->add('seo', SeoFormType::class, [
                                        'label' => 'Мета информация',
                                        'source' => $instance,
                                        'mapped' => false,
                                    ]);
                            }
                        ]);
                }
            ])
    
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
            ])
            ->add('submit_create', SubmitType::class, [
                'label' => 'Сохранить и создать',
            ]);
    }
```


### ButtonsType

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('buttons', ButtonsType::class, [
                'buttons' => function (FormBuilderInterface $builder) {
                    $builder
                        ->add('submit', SubmitType::class, [
                            'label' => 'Сохранить',
                        ])
                        ->add('submit_create', SubmitType::class, [
                            'label' => 'Сохранить и создать',
                        ]);
                }
            ])
    }
```
