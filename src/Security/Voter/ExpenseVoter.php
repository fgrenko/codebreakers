<?php

namespace App\Security\Voter;

use App\Controller\ExpenseController;
use App\Entity\Expense;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\Attribute\Required;

class ExpenseVoter extends Voter
{

    #[Required]
    public Security $security;

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Expense;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case ExpenseController::ROUTE_DELETE:
            case ExpenseController::ROUTE_EDIT:
            case ExpenseController::ROUTE_SHOW:

                return $subject->getCreatedBy() === $user->getId() ? true :
                    throw new AccessDeniedHttpException();

            case ExpenseController::ROUTE_NEW:
                return true;
            case ExpenseController::ROUTE_INDEX:
                return true;
            default:
                throw new AccessDeniedHttpException();
        }

    }
}
