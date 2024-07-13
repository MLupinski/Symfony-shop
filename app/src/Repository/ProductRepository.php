<?php

namespace App\Repository;

use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $to
     *
     * @return mixed
     */
    public function findBetweenDates(DateTimeImmutable $from, DateTimeImmutable $to): mixed
    {
        return $this->createQueryBuilder('p')
            ->where('p.created_at >= :from')
            ->andWhere('p.created_at <= :to')
            ->setMaxResults(4)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $sortField
     * @param string $sortDirection
     *
     * @return QueryBuilder
     */
    public function paginationQueryBuilder(string $sortField = 'p.id', string $sortDirection = 'ASC'): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy($sortField, $sortDirection);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
