<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Enum\OrderStatus;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Translation\t;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $order = new Order(Uuid::v7());
        $order->orderDate = new DateTimeImmutable();
        $order->status = OrderStatus::NEW;

        return $order;
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
            ->setEntityLabelInSingular('Заказ')
            ->setEntityLabelInPlural('Заказы')
            ->setPageTitle('new', 'Создать заказ')
            ->setPageTitle('edit', 'Редактировать заказ');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('client', 'Клиент')
                ->setRequired(true)
                ->setFormTypeOption('choice_label', 'name')
                ->formatValue(function ($value, ?Order $order = null) {
                    return $order?->client?->name ?? 'Не указан';
                }),

            DateTimeField::new('orderDate', 'Дата заказа')
                ->setRequired(true),

            NumberField::new('totalAmount', 'Сумма')
                ->setRequired(false),

            ChoiceField::new('status', 'Статус')
                ->setChoices(
                    array_combine(
                        array_map(
                            fn(OrderStatus $status) => t("orderStatus.{$status->value}"),
                            OrderStatus::cases(),
                        ),
                        OrderStatus::cases(),
                    ),
                )
                ->renderAsBadges(),

            NumberField::new('weight', 'Вес (кг)')
                ->setRequired(false),

            NumberField::new('volume', 'Объем (м³)')
                ->setRequired(false),

            DateTimeField::new('createdAt', 'Создан')
                ->onlyOnIndex()
                ->setFormat('dd.MM.Y HH:mm'),

            DateTimeField::new('updatedAt', 'Обновлен')
                ->onlyOnIndex()
                ->setFormat('dd.MM.Y HH:mm'),
        ];
    }
}
