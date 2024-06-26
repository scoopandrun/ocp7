<?php

// This class normalizes ConstraintViolationListInterface objects
// Path: src/Serializer/Normalizer/ConstraintViolationListNormalizer.php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListNormalizer implements NormalizerInterface
{
    public function normalize($violations, ?string $format = null, array $context = []): array
    {
        /** @var ConstraintViolationListInterface $violations */

        $errors = [];

        // Regroup the violations by property
        $violationsCount = count($violations);
        for ($i = 0; $i < $violationsCount; $i++) {
            $property = $violations[$i]->getPropertyPath();
            $errors[$property][] = $violations[$i]->getMessage();
        }

        return $errors;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ConstraintViolationListInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [ConstraintViolationListInterface::class => true];
    }
}
