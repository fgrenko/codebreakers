<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getCategoriesRankedBySpendings(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.name', 'SUM(e.amount) as totalSpendings')
            ->leftJoin('c.expenses', 'e')
            ->groupBy('c.id')
            ->orderBy('totalSpendings', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getCategoriesRankedBySpendingsInYear(\DateTime $date): array
    {
        $firstDayOfMonth = $date->format('Y-01-01');
        $lastDayOfMonth = $date->format('Y-12-t');

        return $this->createQueryBuilder('c')
            ->select('c.name', 'SUM(e.amount) as totalSpendings')
            ->leftJoin('c.expenses', 'e')
            ->where('e.createdAt BETWEEN :firstDayOfMonth AND :lastDayOfMonth')
            ->setParameter('firstDayOfMonth', $firstDayOfMonth)
            ->setParameter('lastDayOfMonth', $lastDayOfMonth)
            ->groupBy('c.id')
            ->orderBy('totalSpendings', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function getCategoriesRankedBySpendingsInMonth(\DateTime $date): array
    {
        $firstDayOfMonth = $date->format('Y-m-01');
        $lastDayOfMonth = $date->format('Y-m-t');

        return $this->createQueryBuilder('c')
            ->select('c.name', 'SUM(e.amount) as totalSpendings')
            ->leftJoin('c.expenses', 'e')
            ->where('e.createdAt BETWEEN :firstDayOfMonth AND :lastDayOfMonth')
            ->setParameter('firstDayOfMonth', $firstDayOfMonth)
            ->setParameter('lastDayOfMonth', $lastDayOfMonth)
            ->groupBy('c.id')
            ->orderBy('totalSpendings', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getCategoriesRankedBySpendingsInDay(\DateTime $date): array
    {
        $startOfDay = $date->format('Y-m-d 00:00:00');
        $endOfDay = $date->format('Y-m-d 23:59:59');

        return $this->createQueryBuilder('c')
            ->select('c.name', 'SUM(e.amount) as totalSpendings')
            ->leftJoin('c.expenses', 'e')
            ->where('e.createdAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->groupBy('c.id')
            ->orderBy('totalSpendings', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
