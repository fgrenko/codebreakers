<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

class UserController extends AbstractController
{
    #[Required]
    public Security $security;
    #[Required]
    public EntityManagerInterface $entityManager;

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/add_money', name: 'app_add_money')]
    public function addMoney(): RedirectResponse
    {
        $user = $this->security->getUser();
        $user->setMoney($user->getMoney() + 1000);
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        $this->addFlash('success', 'Successfully added funds.');

        return $this->redirect('home');
    }

}
