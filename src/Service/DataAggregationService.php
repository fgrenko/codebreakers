<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Phalcon\Forms\Element\Date;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Service\Attribute\Required;

class DataAggregationService
{
    #[Required]
    public EntityManagerInterface $entityManager;
    #[Required]
    public RequestStack $requestStack;
    #[Required]
    public Security $security;
    #[Required]
    public ExpenseRepository $expenseRepository;
    #[Required]
    public CategoryRepository $categoryRepository;

    public function getAggregatedData(): array
    {
        $aggregatedData = [];
        $datetime = new \DateTime();

        //Spent today
        $expensesDaily = $this->expenseRepository->findExpensesByDate($datetime);

        $aggregatedData['spentToday'] = $this->sumExpensesAmount($expensesDaily);

        //Spent this month
        $dateFrom = new \DateTime($datetime->format("Y-m-01") . " 00:00:00");
        $dateTo = new \DateTime($datetime->format("Y-m-t") . "23:59:59");

        $expensesMonthly = $this->expenseRepository->findExpensesBetweenDates($dateFrom, $dateTo);
        $aggregatedData['spentMonthly'] = $this->sumExpensesAmount($expensesMonthly);

        //Spent this year
        $dateFrom = new \DateTime($datetime->format("Y-01-01") . " 00:00:00");
        $dateTo = new \DateTime($datetime->format("Y-12-t") . "23:59:59");

        $expensesYearly = $this->expenseRepository->findExpensesBetweenDates($dateFrom, $dateTo);
        $aggregatedData['spentYearly'] = $this->sumExpensesAmount($expensesYearly);

        $aggregatedData['categorySpendingsAllTime'] = $this->categoryRepository->getCategoriesRankedBySpendings();
        $aggregatedData['categorySpendingsYearly'] = $this->categoryRepository->getCategoriesRankedBySpendingsInYear($datetime);
        $aggregatedData['categorySpendingsMonthly'] = $this->categoryRepository->getCategoriesRankedBySpendingsInMonth($datetime);
        $aggregatedData['categorySpendingsDaily'] = $this->categoryRepository->getCategoriesRankedBySpendingsInDay($datetime);



        return $aggregatedData;
    }


    public function sumExpensesAmount(array $expenses): float
    {
        $spentAmount = 0;
        foreach ($expenses as $item) {
            $spentAmount += $item->getAmount();
        }

        return $spentAmount;
    }
}
