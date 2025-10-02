<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
                return $action->setIcon('fa fa-eye');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Список клиентов')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Просмотр информации клиента')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавить клиента')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать информацию клиента')
            ->setDefaultSort([
                'createdAt' => 'DESC',
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Наименование'),
            TextField::new('contactPerson', 'Контактное лицо'),
            TextField::new('phone', 'Телефон'),
            EmailField::new('email', 'Email'),
            TextareaField::new('address', 'Адрес'),
            TextField::new('taxNumber', 'ИНН/Налоговый номер'),
            TextareaField::new('paymentTerms', 'Условия оплаты'),
            DateTimeField::new('createdAt', 'Создан')
                ->hideOnForm()
                ->hideOnIndex(),
            DateTimeField::new('updatedAt', 'Обновлен')
                ->hideOnForm()
                ->hideOnIndex(),
        ];
    }
}
