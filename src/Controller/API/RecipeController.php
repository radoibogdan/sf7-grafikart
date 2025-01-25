<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\DTO\PaginationDTO;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecipeController extends AbstractController
{
    /**
     * /api/recipes?page=-3 => erreur car #[Positize] dans DTO
     * #[MapQueryString] Récupère tous les params dans le query string et essaie de le transformer en PaginationDTO puis valider les data
     */
    #[Route('/api/recipes', methods: ['GET'])]
    public function index(
        RecipeRepository $recipeRepository,
        SerializerInterface $serializer,
        #[MapQueryString]
        PaginationDTO $paginationDTO,
    ): Response
    {
//        $recipes = $recipeRepository->paginateRecipesKnpPaginator($request->query->getInt('page', 1));
        $recipes = $recipeRepository->paginateRecipesKnpPaginator($paginationDTO->page);

        /* Le PaginationNormalizer a été créé pour renvoyer un format incluant
        {
            "items": [...],
            "total": 5,
            "page": 1,
            "lastPage": 3
        }*/

        /* On peut aussi renvoyer du csv, xml, yaml */
//        dd($serializer->serialize($recipes, 'xml', [
//            'groups' => ['recipe_index'],
//        ]));

        return $this->json($recipes,Response::HTTP_OK, [], [
            'groups' => ['recipe.index']
        ]);
    }

    #[Route('/api/recipes/{id}', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Recipe $recipe): Response
    {
        return $this->json($recipe, Response::HTTP_OK, [], [
            'groups' => ['recipe.index', 'recipe.show']
        ]);
    }

    // TRANSFORM ARRAY TO OBJECT - V1
    #[Route('/api/recipes/deserializer-v1', methods: ['POST'])]
    public function create(
        Request $request,
        SerializerInterface $serializer,
    ): Response
    {
        $recipe = new Recipe();
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        // OBJECT TO POPULATE => au lieu de créer un nouveau objet il va rajouter l'info dans l'objet passé en param
        // groups => limiter les champs que l'utilsateur peur modifier
        // Une fois que l'objet est mis à jour on peut injecter le Validator et valider l'entité.
        dd($serializer->deserialize($request->getContent(), Recipe::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
            'groups' => ['recipe.create']
        ]));
    }

    // TRANSFORM ARRAY TO OBJECT
    #[Route('/api/recipes/deserializer-v2', methods: ['POST'])]
    public function createWithPayload(
        Request $request,
        // Le payload de la méthode (le json) va être injecté dans la méthode,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['recipe.create'],
            ]
        )]
        Recipe $recipe,
        EntityManagerInterface $em,
    ): Response
    {
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($recipe);
        $em->flush();
        return $this->json($recipe, Response::HTTP_CREATED, [], [
            'groups' => ['recipe.index', 'recipe.show']
        ]);
    }
}
