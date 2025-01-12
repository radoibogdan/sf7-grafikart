<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllWithCount(): array
    {
        return $this->createQueryBuilder('c')
            ->select('NEW App\\DTO\\CategoryWithCountDTO(c.id, c.name, COUNT(c.id))')
            ->leftJoin('c.recipes', 'r')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        /*DQL equivalent*/
        $this->getEntityManager()->createQuery(<<<DQL
            SELECT NEW APP\\DTO\\CategoryWithCountDTO(c.id, c.name, COUNT(c.id))
            FROM App\Entity\Category c
            LEFT JOIN c.recipes r
            GROUP by c.id
            DQL)->getResult();
    }
}
