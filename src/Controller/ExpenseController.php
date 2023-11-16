<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\ExpenseFilterType;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use App\Service\MoneyDeductionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[Route('/expense')]
class ExpenseController extends AbstractController
{
    #[Required]
    public Security $security;
    #[Required]
    public MoneyDeductionService $moneyDeductionService;

    #[Route('/', name: 'app_expense_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ExpenseRepository $expenseRepository): Response
    {

        $form = $this->createForm(ExpenseFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenses = $expenseRepository->findByFilter($form->getData());
        } else {
            $expenses = $expenseRepository->findAll();
        }

        return $this->render('expense/index.html.twig', [
            'expenses' => $expenses,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_expense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $expense->setCreatedBy($user);

            if ($this->moneyDeductionService->deduct($expense)) {
                $entityManager->persist($expense);
                $entityManager->flush();
            } else {
                $this->addFlash('error', "You don't have sufficient funds to pay for this service");
            }


            return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expense/new.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_show', methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expense/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_delete', methods: ['DELETE'])]
    public function delete(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
    }
}
