<?php

namespace App\Admin;

use App\Entity\Client;
use App\Entity\Order;
use App\Enum\OrderStatus;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Uid\Uuid;

final class OrderAdmin extends AbstractAdmin
{
    protected function configure(): void
    {
        $this->setTranslationDomain('sonataAdmin');
    }

    protected function createNewInstance(): object
    {
        return new Order(Uuid::v7());
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with('Основная информация');
        $form->add('client', EntityType::class, [
            'class' => Client::class,
            'choice_label' => 'name',
            'label' => 'Клиент'
        ]);
        $form->add('orderDate', DateTimePickerType::class, [
            'label' => 'Дата заказа',
            'format' => 'dd.MM.yyyy HH:mm'
        ]);
        $form->add('status', ChoiceType::class, [
            'label' => 'Статус',
            'choices' => OrderStatus::cases(),
            'choice_label' => function (OrderStatus $status) {
                return $status->value;
            },
            'choice_value' => function (?OrderStatus $status) {
                return $status?->value;
            }
        ]);
        $form->end();

        $form->with('Финансовая информация');
        $form->add('totalAmount', NumberType::class, [
            'label' => 'Сумма заказа',
            'required' => false,
            'scale' => 2,
            'attr' => [
                'step' => '0.01',
                'min' => '0',
                'max' => '999999.99'
            ]
        ]);
        $form->end();

        $form->with('Характеристики груза');
        $form->add('weight', NumberType::class, [
            'label' => 'Вес (кг)',
            'required' => false,
            'attr' => [
                'min' => '0',
                'max' => '100000'
            ]
        ]);
        $form->add('volume', NumberType::class, [
            'label' => 'Объем (м³)',
            'required' => false,
            'scale' => 2,
            'attr' => [
                'step' => '0.01',
                'min' => '0',
                'max' => '10000'
            ]
        ]);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add(
            'client',
            null,
            [
            'label' => 'Клиент'
        ], [
            'class' => Client::class,
            'choice_label' => 'name',
        ]);
        $datagrid->add('orderDate', null, [
            'label' => 'Дата заказа'
        ]);
        $datagrid->add(
            'status',
            null,
            [
                'label' => 'Статус'
            ],
            [
            'choices' => OrderStatus::cases(),
            'choice_label' => function (OrderStatus $status) {
                return $status->value;
            }
        ]);
        $datagrid->add('totalAmount', null, [
            'label' => 'Сумма заказа'
        ]);
        $datagrid->add('weight', null, [
            'label' => 'Вес (кг)'
        ]);
        $datagrid->add('volume', null, [
            'label' => 'Объем (м³)'
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->add('client', null, [
            'label' => 'Клиент',
            'associated_property' => 'name'
        ]);
        $list->add('orderDate', 'datetime', [
            'label' => 'Дата заказа',
            'format' => 'd.m.Y H:i'
        ]);
        $list->add('status', null, [
            'label' => 'Статус'
        ]);
        $list->add('totalAmount', 'currency', [
            'label' => 'Сумма',
            'currency' => 'RUB'
        ]);
        $list->add('weight', null, [
            'label' => 'Вес (кг)'
        ]);
        $list->add('volume', null, [
            'label' => 'Объем (м³)'
        ]);
//        $list->add('_action', 'actions', [
//            'label' => 'Действия',
//            'actions' => [
//                'show' => [],
//                'edit' => [],
//                'delete' => [],
//            ],
//        ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->with('Основная информация');
        $show->add('client', null, [
            'label' => 'Клиент',
            'associated_property' => 'name'
        ]);
        $show->add('orderDate', 'datetime', [
            'label' => 'Дата заказа',
            'format' => 'd.m.Y H:i'
        ]);
        $show->add('status', null, [
            'label' => 'Статус'
        ]);
        $show->end();

        $show->with('Финансовая информация');
        $show->add('totalAmount', 'currency', [
            'label' => 'Сумма заказа',
            'currency' => 'RUB'
        ]);
        $show->end();

        $show->with('Характеристики груза');
        $show->add('weight', null, [
            'label' => 'Вес (кг)'
        ]);
        $show->add('volume', null, [
            'label' => 'Объем (м³)'
        ]);
        $show->end();

        $show->with('Системная информация');
        $show->add('createdAt', 'datetime', [
            'label' => 'Создан',
            'format' => 'd.m.Y H:i'
        ]);
        $show->add('updatedAt', 'datetime', [
            'label' => 'Обновлен',
            'format' => 'd.m.Y H:i'
        ]);
        $show->end();
    }

    public function toString(object $object): string
    {
        return $object instanceof Order
            ? (string) $object
            : 'Заказ';
    }
}
