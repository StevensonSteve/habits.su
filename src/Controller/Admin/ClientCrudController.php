<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Uid\Uuid;

class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return new Client(Uuid::v7());
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action
                    ->setIcon('fa fa-eye')
                    ->setLabel('admin.action.detail');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action
                    ->setIcon('fa fa-edit')
                    ->setLabel('admin.action.edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action
                    ->setIcon('fa fa-trash')
                    ->setLabel('admin.action.delete');
            })
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('client.action.new');
            });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'client.pageTitle.index')
            ->setPageTitle(Crud::PAGE_DETAIL, 'client.pageTitle.detail')
            ->setPageTitle(Crud::PAGE_NEW, 'client.pageTitle.new')
            ->setPageTitle(Crud::PAGE_EDIT, 'client.pageTitle.edit')
            ->setEntityLabelInSingular('client.entity.singular')
            ->setEntityLabelInPlural('client.entity.plural')
            ->setDefaultSort([
                'createdAt' => 'DESC',
            ])
            ->setSearchFields(['name', 'contactPerson', 'email', 'taxNumber']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            CollectionField::new('orders', 'client.field.orders')
                ->useEntryCrudForm()
                ->onlyOnDetail(),
            TextField::new('name', 'client.field.name')
                ->setRequired(true),

            TextField::new('contactPerson', 'client.field.contactPerson')
                ->setRequired(true),

            TextField::new('phone', 'client.field.phone')
                ->setRequired(true),

            EmailField::new('email', 'client.field.email')
                ->setRequired(true),

            TextareaField::new('address', 'client.field.address')
                ->hideOnIndex(),

            TextField::new('taxNumber', 'client.field.taxNumber')
                ->setRequired(true),

            TextareaField::new('paymentTerms', 'client.field.paymentTerms')
                ->hideOnIndex(),

            DateTimeField::new('createdAt', 'client.field.createdAt')
                ->hideOnForm()
                ->hideOnIndex(),
            DateTimeField::new('updatedAt', 'client.field.updatedAt')
                ->hideOnForm()
                ->hideOnIndex(),
        ];
    }
}
