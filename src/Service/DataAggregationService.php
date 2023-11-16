<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class DataAggregationService
{
    #[Required]
    public EntityManagerInterface $entityManager;
    #[Required]
    public CategoryRepository $categoryRepository;

    public function getAggregatedData(): array
    {
        $aggregatedData = [];
        $datetime = new \DateTime();

        //spendings by category
        $aggregatedData['categorySpendingsAllTime'] = $this->categoryRepository->getCategoriesRankedBySpendings();
        $aggregatedData['categorySpendingsYear'] = $this->categoryRepository->getCategoriesRankedBySpendingsInYear($datetime);
        $aggregatedData['categorySpendingsMonth'] = $this->categoryRepository->getCategoriesRankedBySpendingsInMonth($datetime);
        $aggregatedData['categorySpendingsDay'] = $this->categoryRepository->getCategoriesRankedBySpendingsInDay($datetime);

        $aggregatedData['spentToday'] = $this->sumExpensesAmount($aggregatedData['categorySpendingsDay']);
        $aggregatedData['spentMonth'] = $this->sumExpensesAmount($aggregatedData['categorySpendingsMonth']);
        $aggregatedData['spentYear'] = $this->sumExpensesAmount($aggregatedData['categorySpendingsYear']);
        $aggregatedData['spentAllTime'] = $this->sumExpensesAmount($aggregatedData['categorySpendingsAllTime']);

        return $aggregatedData;
    }


    public function sumExpensesAmount(array $categories): float
    {
        $amount = 0;
        foreach ($categories as $item) {
            $amount += $item['totalSpendings'];
        }

        return $amount;
    }
}
