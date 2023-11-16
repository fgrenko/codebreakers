<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\ExpenseFilterType;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use App\Service\MoneyDeductionService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[Route('/expense')]
class ExpenseController extends AbstractController
{
    public const EXPENSE_SORT_FIELDS = [
        'Created At' => 'createdAt',
        'Category' => 'category',
        'Amount' => 'amount',
        'Description' => 'description',
    ];

    public const ROUTE_DELETE = 'app_expense_delete';
    public const ROUTE_EDIT = 'app_expense_edit';
    public const ROUTE_INDEX = 'app_expense_index';
    public const ROUTE_NEW = 'app_expense_new';
    public const ROUTE_SHOW = 'app_expense_show';

    #[Required]
    public Security $security;
    #[Required]
    public MoneyDeductionService $moneyDeductionService;

    #[Route('/', name: self::ROUTE_INDEX, methods: ['GET', 'POST'])]
    public function index(Request $request, ExpenseRepository $expenseRepository): Response
    {

        $form = $this->createForm(ExpenseFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenses = $expenseRepository->findByFilter($form->getData());
        } else {
            $expenses = $expenseRepository->findByFilter(null);
        }

        $adapter = new QueryAdapter($expenses);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage($adapter, $request->query->get('page', 1), 10);

        return $this->render('expense/index.html.twig', [
            'expenses' => $pagerfanta,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: self::ROUTE_NEW, methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $expense = new Expense();
        $this->denyAccessUnlessGranted(self::ROUTE_NEW, $expense);
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

    #[Route('/{id}', name: self::ROUTE_SHOW, methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        $this->denyAccessUnlessGranted(self::ROUTE_SHOW, $expense);
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/edit', name: self::ROUTE_EDIT, methods: ['GET', 'POST'])]
    public function edit(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(self::ROUTE_EDIT, $expense);
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

    #[Route('/{id}', name: self::ROUTE_DELETE, methods: ['DELETE'])]
    public function delete(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(self::ROUTE_DELETE, $expense);
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
    }
}
