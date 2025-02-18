<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function paginateRecipesKnpPaginator(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->select('r', 'c');

        if ($userId) {
            $builder->andWhere('r.user = :userId')
                ->setParameter('userId', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            10,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['r.id', 'r.title']
            ]
        );
    }

    public function paginateRecipes(int $page, int $limit)
    {
        /* Paginator , 2eme param - false => ne pas récupérer les left join */
        return new Paginator($this->createQueryBuilder('r')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            /* Si on sait qu'on aura pas plusieurs résultats on peut enlever le distinct */
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false)
            , false);
    }



    public function findTotalDuration(): int
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Recipe[]
     */
    public function findWithDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            // récupère les categories aussi par défaut pour eviter les problème N+1 (recipe.category.name gènèreu ne nouvelle requête)
            ->select('r', 'c')
            ->leftJoin('r.category', 'c')
            ->where('r.duration < :duration')
            ->orderBy('r.duration', 'ASC')
            ->setMaxResults(10)
            ->setParameter('duration', $duration)
            ->getQuery()->getResult();
    }
}
