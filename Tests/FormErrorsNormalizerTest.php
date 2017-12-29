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

use Mindy\Bundle\FormBundle\Normalizer\FormErrorsNormalizer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class FormErrorsNormalizerTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testFormNormalizer()
    {
        $form = $this->factory
            ->createBuilder()
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->submit([]);
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());

        $serializer = new Serializer([
            new FormErrorsNormalizer(),
        ], [
            new JsonEncoder(),
        ]);
        $this->assertSame([
            'name' => ['This value should not be blank.'],
        ], json_decode($serializer->serialize($form, 'json'), true));
        $this->assertSame([
            'name' => ['This value should not be blank.'],
        ], json_decode($serializer->serialize($form->getErrors(true, false), 'json'), true));
    }
}
