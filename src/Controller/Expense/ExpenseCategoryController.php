<?php

namespace App\Controller\Expense;

use App\Entity\Expense\ExpenseCategory;
use App\Form\Expense\ExpenseCategoryType;
use App\Repository\Expense\ExpenseCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/expense/categories')]
final class ExpenseCategoryController extends AbstractController
{
    #[Route(name: 'app_expense_category_index', methods: ['GET'])]
    public function index(ExpenseCategoryRepository $expenseCategoryRepository): Response
    {
        return $this->render('expense/expense_category/index.html.twig', [
            'expense_categories' => $expenseCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_expense_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $expenseCategory = new ExpenseCategory(Uuid::v7(), '');
        $form = $this->createForm(ExpenseCategoryType::class, $expenseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($expenseCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_expense_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expense/expense_category/new.html.twig', [
            'expense_category' => $expenseCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_category_show', methods: ['GET'])]
    public function show(ExpenseCategory $expenseCategory): Response
    {
        return $this->render('expense/expense_category/show.html.twig', [
            'expense_category' => $expenseCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expense_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExpenseCategory $expenseCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExpenseCategoryType::class, $expenseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_expense_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('expense/expense_category/edit.html.twig', [
            'expense_category' => $expenseCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_category_delete', methods: ['POST'])]
    public function delete(Request $request, ExpenseCategory $expenseCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expenseCategory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($expenseCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_expense_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
