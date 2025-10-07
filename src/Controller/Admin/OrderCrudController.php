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
                return $action->setLabel('order.action.new');
            });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'order.pageTitle.index')
            ->setPageTitle(Crud::PAGE_DETAIL, 'order.pageTitle.detail')
            ->setPageTitle(Crud::PAGE_NEW, 'order.pageTitle.new')
            ->setPageTitle(Crud::PAGE_EDIT, 'order.pageTitle.edit')
            ->setEntityLabelInSingular('order.entity.singular')
            ->setEntityLabelInPlural('order.entity.plural')
            ->setDefaultSort([
                'createdAt' => 'DESC',
            ])
            ->setSearchFields(['id', 'client.name']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('client', 'order.field.client')
                ->setRequired(true)
                ->setFormTypeOption('choice_label', 'name')
                ->formatValue(function ($value, ?Order $order = null) {
                    return $order?->client?->name ?? 'order.field.client_not_found';
                }),

            DateTimeField::new('orderDate', 'order.field.orderDate')
                ->setRequired(true)
                ->setFormat('dd.MM.Y HH:mm'),

            NumberField::new('totalAmount', 'order.field.totalAmount')
                ->setNumDecimals(2)
                ->setNumberFormat('%.2f')
                ->setRequired(false),

            ChoiceField::new('status', 'order.field.status')
                ->setChoices(
                    array_combine(
                        array_map(
                            fn(OrderStatus $status) => "order.status.{$status->value}",
                            OrderStatus::cases(),
                        ),
                        OrderStatus::cases(),
                    ),
                )
                ->renderAsBadges(),

            NumberField::new('weight', 'order.field.weight')
                ->setNumDecimals(0)
                ->setRequired(false)
                ->hideOnIndex(),

            NumberField::new('volume', 'order.field.volume')
                ->setNumDecimals(3)
                ->setNumberFormat('%.3f')
                ->setRequired(false)
                ->hideOnIndex(),

            DateTimeField::new('createdAt', 'order.field.createdAt')
                ->onlyOnDetail()
                ->setFormat('dd.MM.Y HH:mm'),

            DateTimeField::new('updatedAt', 'order.field.updatedAt')
                ->onlyOnDetail()
                ->setFormat('dd.MM.Y HH:mm'),
        ];
    }
}
