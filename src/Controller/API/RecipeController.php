<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RecipeController extends AbstractController
{
    #[Route('/api/recipes', name: 'api.recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, Request $request, SerializerInterface $serializer): Response
    {
        $recipes = $recipeRepository->paginateRecipesKnpPaginator($request->query->getInt('page', 1));

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

        return $this->json($recipes, Response::HTTP_OK, [], ['groups' => ['recipe.index']]);
    }
    #[Route('/api/recipe/{id}', name: 'api.recipe.show', methods: ['GET'])]
    public function show(Recipe $recipe): Response
    {
        return $this->json($recipe, Response::HTTP_OK, [], ['groups' => ['recipe.index', 'recipe.show']]);
    }
}
