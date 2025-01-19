<?php

namespace App\Normalizer;

use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginationNormalizer implements NormalizerInterface
{
    public function __construct(
        // on fait ça pour pas avoir un loop infini
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return [
            'items' => array_map(
                fn(Recipe $recipe) => $this->normalizer->normalize($recipe, $format, $context),
                $data->getItems()
            ),
            'total' => $data->getTotalItemCount(),
            'page' => $data->getCurrentPageNumber(),
            'lastPage' => ceil($data->getTotalItemCount() / $data->getItemNumberPerPage()),
        ];
    }

    /**
     * @param string|null $format csv, json autre
     * @param array $context les groupes se trouvent ici
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        // renvoie OUI/NON s'il doit agir sur l'objet que l'on reçoit
        return $data instanceof PaginationInterface
//            && $format === 'json'
        ;
    }

    public function getSupportedTypes(?string $format): array
    {
        // Ce normalizer est déclenché que quand on a qqc qui implemente la PaginationInterface
        return [
            PaginationInterface::class => true,
        ];
    }
}