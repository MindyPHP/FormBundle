<?php

declare(strict_types=1);

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\Normalizer;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class FormErrorsNormalizer implements NormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($object instanceof FormErrorIterator) {
            return $this->iterateFormErrors($object);
        }

        return $this->iterateFormErrors($object->getErrors(true, false));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof FormInterface || $data instanceof FormErrorIterator;
    }

    /**
     * @param $iterator
     *
     * @return array
     */
    protected function iterateFormErrors($iterator): array
    {
        $errors = [];
        foreach ($iterator as $error) {
            if ($error instanceof FormError) {
                $errors[] = $error->getMessage();
            } else {
                /* @var $error FormErrorIterator */
                $errors[$error->getForm()->getName()] = $this->iterateFormErrors($error);
            }
        }

        return $errors;
    }
}
