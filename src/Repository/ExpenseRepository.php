<?php

namespace App\Repository;

use App\Entity\Expense;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function findByFilter($filter)
    {
        $queryBuilder = $this->createQueryBuilder('e');

        if (isset($filter['category'])) {
            $queryBuilder
                ->andWhere('e.category = :category')
                ->setParameter('category', $filter['category']);
        }
        if (isset($filter['priceMin'])) {
            $queryBuilder
                ->andWhere('e.amount >= :priceMin')
                ->setParameter('priceMin', $filter['priceMin']);
        }
        if (isset($filter['priceMax'])) {
            $queryBuilder
                ->andWhere('e.amount <= :priceMax')
                ->setParameter('priceMax', $filter['priceMax']);
        }
        if (isset($filter['createdAt'])) {
            $from = new \DateTime($filter['createdAt']->format("Y-m-d") . " 00:00:00");
            $to = new \DateTime($filter['createdAt']->format("Y-m-d") . " 23:59:59");

            // Compare only the date part
            $queryBuilder
                ->andWhere('e.createdAt BETWEEN :from AND :to')
                ->setParameter('from', $from)
                ->setParameter('to', $to);
        }
        if (isset($filter['sortItem'])) {
            $queryBuilder->orderBy('e.' . $filter['sortItem'], $filter['sortType']);
        }

        return $queryBuilder;
    }

    public function findExpensesByDate(DateTime $date)
    {
        $from = new \DateTime($date->format("Y-m-d") . " 00:00:00");
        $to = new \DateTime($date->format("Y-m-d") . " 23:59:59");

        return $this->createQueryBuilder('e')
            ->andWhere('e.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }


    public function findExpensesBetweenDates(DateTime $dateFrom, Datetime $dateTo)
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $dateFrom)
            ->setParameter('to', $dateTo)
            ->getQuery()
            ->getResult();
    }
}
