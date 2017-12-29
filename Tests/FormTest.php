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

use Mindy\Bundle\FormBundle\Form\DataTransformer\QuerySetTransformer;
use Mindy\Bundle\FormBundle\Form\Extension\CheckboxExtension;
use Mindy\Bundle\FormBundle\Form\Extension\HelpExtension;
use Mindy\Bundle\FormBundle\Form\Extension\QuerySetExtension;
use Mindy\Bundle\FormBundle\Form\Extension\TooltipExtension;
use Mindy\Bundle\FormBundle\Form\Type\FileEvent;
use Mindy\Bundle\FormBundle\Form\Type\FileType;
use Mindy\Bundle\FormBundle\Form\Type\SlugType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType as BaseFileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class);
    }
}

class FormTest extends TypeTestCase
{
    public function testHelpExtension()
    {
        $form = $this->factory->create(TextType::class);

        $ext = new HelpExtension();

        $view = new FormView();
        $ext->buildView($view, $form, ['help' => 'foo bar']);
        $this->assertSame('foo bar', $view->vars['help']);

        $resolver = new OptionsResolver();
        $ext->configureOptions($resolver);
        $this->assertSame(['help'], $resolver->getDefinedOptions());

        $this->assertSame(FormType::class, $ext->getExtendedType());
    }

    public function testTooltipExtension()
    {
        $form = $this->factory->create(TextType::class);

        $ext = new TooltipExtension();

        $view = new FormView();
        $ext->buildView($view, $form, ['tooltip' => 'foo bar']);
        $this->assertSame('foo bar', $view->vars['tooltip']);

        $resolver = new OptionsResolver();
        $ext->configureOptions($resolver);
        $this->assertSame(['tooltip'], $resolver->getDefinedOptions());

        $this->assertSame(FormType::class, $ext->getExtendedType());
    }

    public function testQuerySetExtension()
    {
        $ext = new QuerySetExtension(new QuerySetTransformer());

        $ext->buildForm($this->builder, []);
        $this->assertCount(0, $this->builder->getModelTransformers());

        $ext->buildForm($this->builder, ['multiple' => true]);
        $this->assertCount(1, $this->builder->getModelTransformers());

        $this->assertSame(ChoiceType::class, $ext->getExtendedType());
    }

    public function testCheckboxExtension()
    {
        $ext = new CheckboxExtension();
        $this->assertSame(CheckboxType::class, $ext->getExtendedType());

        $ext->buildForm($this->builder, []);
        $this->assertCount(1, $this->builder->getModelTransformers());
    }

    public function testFileType()
    {
        $requestStack = $this
            ->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $type = $this->getFileType();
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);
        $this->assertSame(['asset_name'], $resolver->getDefinedOptions());
        $this->assertSame('mfile', $type->getBlockPrefix());
        $this->assertSame(BaseFileType::class, $type->getParent());
    }

    public function testResolveUrl()
    {
        $type = $this->getFileType();

        $this->assertSame('123', $type->resolveFileUrl(['name' => '123'], 'name'));

        $data = new \stdClass();
        $data->name = '123';
        $this->assertSame('123', $type->resolveFileUrl($data, 'name'));

        $data = new \stdClass();
        $data->name = 123;
        $this->assertSame('', $type->resolveFileUrl($data, 'name'));
    }

    public function testFileTypeView()
    {
        $form = $this->factory->create(UploadForm::class);
        $formView = $form->createView();
        $view = current($formView->children);
        $this->assertArrayHasKey('asset_name', $view->vars);
        $this->assertArrayHasKey('file_url', $view->vars);
        $this->assertSame('', $view->vars['file_url']);

        $form = $this->factory->create(FileType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('file_url', $view->vars);
        $this->assertSame('', $view->vars['file_url']);
    }

    protected function getFileType(Request $request = null)
    {
        if (null === $request) {
            $request = new Request();
        }
        $requestStack = $this
            ->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestStack->method('getCurrentRequest')->willReturn($request);

        return new FileType(new FileEvent($requestStack));
    }

    protected function getTypes()
    {
        return [
            $this->getFileType(),
            new SlugType(),
        ];
    }

    public function testFileEvent()
    {
        // Send empty form, do nothing. Dont remove file type from parent form, dont clean value
        $factory = Forms::createFormFactoryBuilder()->addTypes([
            $this->getFileType(new Request([], ['upload_form' => []])),
        ])->getFormFactory();

        $form = $factory->create(UploadForm::class);
        $this->assertTrue($form->has('file'));
        $form->submit([]);
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->has('file'));

        // Remove file field from parent form to avoid unset value
        $factory = Forms::createFormFactoryBuilder()->addTypes([
            $this->getFileType(new Request([], ['upload_form' => ['file' => null]])),
        ])->getFormFactory();
        $form = $factory->create(UploadForm::class);
        $this->assertTrue($form->has('file'));
        $form->submit([]);
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->has('file'));

        // If file field marked "For remove" then clean field value
        $factory = Forms::createFormFactoryBuilder()->addTypes([
            $this->getFileType(new Request([], ['upload_form' => ['file' => FileEvent::CLEAR_VALUE]])),
        ])->getFormFactory();
        $form = $factory->create(UploadForm::class);
        $this->assertTrue($form->has('file'));
        $form->submit([]);
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->has('file'));
    }

    public function testSlugType()
    {
        $slug = new SlugType();
        $this->assertSame(TextType::class, $slug->getParent());

        $form = $this->factory
            ->createBuilder(SlugType::class, '/foo/bar')
            ->getForm();

        $view = $form->createView();
        $this->assertSame('bar', $view->vars['value']);
    }
}
