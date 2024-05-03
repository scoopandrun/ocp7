<?php

// Path: src/Normalizer/PaginatorNormalizer.php

namespace App\Normalizer;

use App\DTO\PaginationDTO;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PaginatorNormalizer implements ContextAwareNormalizerInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($paginator, ?string $format = null, array $context = [])
    {
        /** @var Paginator $paginator */

        $items = array_map(function ($item) use ($format, $context) {
            return $this->normalizer->normalize($item, $format, $context);
        }, (array) $paginator->getIterator());

        $data = [];

        $data["total"] = $paginator->count();

        // If the "pagination" key is set in the context, add pagination information
        if (isset($context["pagination"]) && $context["pagination"] instanceof PaginationDTO) {
            $data["page"] = $context["pagination"]->page;
            $data["pageSize"] = $context["pagination"]->limit;
        }

        $data["items"] = $items;

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = [])
    {
        return $data instanceof Paginator;
    }
}
