<?php

namespace App\Admin;

use App\Entity\Client;
use App\Factory\ClientFactory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Uid\Uuid;

final class ClientAdmin extends AbstractAdmin
{
    protected function createNewInstance(): object
    {
        return new Client(Uuid::v7());
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with('Основная информация');
        $form->add('name', TextType::class);
        $form->add('contactPerson', TextType::class);
        $form->add('phone', TextType::class, ['label' => 'Телефон']);
        $form->add('email', EmailType::class, ['label' => 'Email']);
        $form->end();

        $form->with('Юридическая информация');
        $form->add('taxNumber', TextType::class, [
            'label' => 'Налоговый номер'
        ]);
        $form->add('address', TextareaType::class, [
            'label' => 'Адрес',
            'required' => false,
            'attr' => ['rows' => 3]
        ]);
        $form->end();
        $form->with('Дополнительно');
        $form->add('paymentTerms', TextareaType::class, [
            'label' => 'Условия оплаты',
            'required' => false,
            'attr' => ['rows' => 3]
        ]);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name', null, [
            'label' => 'Название компании'
        ]);
        $datagrid->add('contactPerson', null, [
            'label' => 'Контактное лицо'
        ]);
        $datagrid->add('phone', null, [
            'label' => 'Телефон'
        ]);
        $datagrid->add('email', null, [
            'label' => 'Email'
        ]);
        $datagrid->add('taxNumber', null, [
            'label' => 'Налоговый номер'
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name', null, [
            'label' => 'Название компании'
        ]);
        $list->add('contactPerson', null, [
            'label' => 'Контактное лицо'
        ]);
        $list->add('phone', null, [
            'label' => 'Телефон'
        ]);
        $list->add('email', null, [
            'label' => 'Email'
        ]);
        $list->add('taxNumber', null, [
            'label' => 'Налоговый номер'
        ]);
        $list->add('createdAt', 'datetime', [
            'label' => 'Создан',
            'format' => 'd.m.Y H:i'
        ]);
        $list->add('updatedAt', 'datetime', [
            'label' => 'Обновлен',
            'format' => 'd.m.Y H:i'
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
        $show->add('name', null, [
            'label' => 'Название компании'
        ]);
        $show->add('contactPerson', null, [
            'label' => 'Контактное лицо'
        ]);
        $show->add('phone', null, [
            'label' => 'Телефон'
        ]);
        $show->add('email', null, [
            'label' => 'Email'
        ]);
        $show->end();
        $show->with('Юридическая информация');
        $show->add('taxNumber', null, [
            'label' => 'Налоговый номер'
        ]);
        $show->add('address', null, [
            'label' => 'Адрес'
        ]);
        $show->end();
        $show->with('Дополнительно');
        $show->add('paymentTerms', null, [
            'label' => 'Условия оплата'
        ]);
        $show->add('createdAt', 'datetime', [
            'label' => 'Создан',
            'format' => 'd.m.Y H:i'
        ]);
        $show->add('updatedAt', 'datetime', [
            'label' => 'Обновлен',
            'format' => 'd.m.Y H:i'
        ]);
        $show->end();
        $show->with('Заказы');
        $show->add('orders', null, [
            'label' => 'Заказы',
            'associated_property' => function ($order) {
                return $order->getId();
            }
        ]);
        $show->end();
    }

    public function toString(object $object): string
    {
        return $object instanceof Client
            ? $object->name
            : 'Клиент';
    }
}
