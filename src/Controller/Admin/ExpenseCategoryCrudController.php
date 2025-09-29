<?php

namespace App\Controller\Admin;

use App\Entity\Expense\ExpenseCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Uid\Uuid;

class ExpenseCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ExpenseCategory::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return new ExpenseCategory(
            Uuid::v7(),
            '',
        );
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextEditorField::new('description'),
        ];
    }
}
