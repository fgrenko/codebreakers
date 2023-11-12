<?php

namespace App\Service;

use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\Attribute\Required;

class MoneyDeductionService
{
    #[Required]
    public EntityManagerInterface $entityManager;
    #[Required]
    public RequestStack $requestStack;
    #[Required]
    public Security $security;

    public function deduct(Expense $expense): bool
    {
        $user = $this->security->getUser();

        if ($this->canDeductMoney($user, $expense)) {
            $user->setMoney($user->getMoney() - $expense->getAmount());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    private function canDeductMoney(UserInterface $user, Expense $expense): bool
    {
        return $user->getMoney() >= $expense->getAmount();
    }
}
