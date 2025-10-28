<?php

namespace App\Admin;

use App\Entity\Client;
use App\Entity\Order;
use App\Enum\OrderStatus;
use App\Notification\Message\TelegramNotification;
use App\Scheduler\Order\Message\BrokerMessage;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class OrderAdmin extends AbstractAdmin
{
    private MessageBusInterface $bus;

    public function setMessageBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }

    protected function configure(): void
    {
        $this->setTranslationDomain('sonataAdmin');
    }

    protected function createNewInstance(): object
    {
        return new Order(Uuid::v7());
    }

    protected function postPersist(object $object): void
    {
        /** @var Order $order */
        $order = $object;

        $this->bus->dispatch(new TelegramNotification(
            sprintf('TelegramNotification: Новый заказ #%s создан!', $order->getId()),
        ));
        $this->bus->dispatch(new BrokerMessage(
            sprintf('BrokerMessage: Новый заказ #%s создан!', $order->getId()),
        ));

        parent::postPersist($object);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with('order.pageTitle.edit');
        $form->add('client', EntityType::class, [
            'class' => Client::class,
            'choice_label' => 'name',
            'label' => 'order.field.client',
        ]);
        $form->add('orderDate', DateTimePickerType::class, [
            'label' => 'order.field.orderDate',
            'format' => 'dd.MM.yyyy HH:mm',
        ]);
        $form->add('status', ChoiceType::class, [
            'label' => 'order.field.status',
            'choices' => OrderStatus::cases(),
            'choice_label' => fn(OrderStatus $status) => $status->value,
            'choice_value' => fn(?OrderStatus $status) => $status?->value,
        ]);
        $form->end();

        $form->with('order.pageTitle.edit');
        $form->add('totalAmount', NumberType::class, [
            'label' => 'order.field.totalAmount',
            'required' => false,
            'scale' => 2,
            'attr' => [
                'step' => '0.01',
                'min' => '0',
                'max' => '999999.99',
            ],
        ]);
        $form->end();

        $form->with('order.pageTitle.edit');
        $form->add('weight', NumberType::class, [
            'label' => 'order.field.weight',
            'required' => false,
            'attr' => [
                'min' => '0',
                'max' => '100000',
            ],
        ]);
        $form->add('volume', NumberType::class, [
            'label' => 'order.field.volume',
            'required' => false,
            'scale' => 2,
            'attr' => [
                'step' => '0.01',
                'min' => '0',
                'max' => '10000',
            ],
        ]);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('client', null, [
            'label' => 'order.field.client',
        ], [
            'class' => Client::class,
            'choice_label' => 'name',
        ]);
        $datagrid->add('orderDate', null, [
            'label' => 'order.field.orderDate',
        ]);
        $datagrid->add('status', null, [
            'label' => 'order.field.status',
        ], [
            'choices' => OrderStatus::cases(),
            'choice_label' => fn(OrderStatus $status) => $status->value,
        ]);
        $datagrid->add('totalAmount', null, [
            'label' => 'order.field.totalAmount',
        ]);
        $datagrid->add('weight', null, [
            'label' => 'order.field.weight',
        ]);
        $datagrid->add('volume', null, [
            'label' => 'order.field.volume',
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->add('client', null, [
            'label' => 'order.field.client',
            'associated_property' => 'name',
        ]);
        $list->add('orderDate', 'datetime', [
            'label' => 'order.field.orderDate',
            'format' => 'd.m.Y H:i',
        ]);
        $list->add('status', null, [
            'label' => 'order.field.status',
        ]);
        $list->add('totalAmount', 'currency', [
            'label' => 'order.field.totalAmount',
            'currency' => 'RUB',
        ]);
        $list->add('weight', null, [
            'label' => 'order.field.weight',
        ]);
        $list->add('volume', null, [
            'label' => 'order.field.volume',
        ]);
        // $list->add('_action', 'actions', [
        //     'label' => 'admin.action',
        //     'actions' => [
        //         'show' => [],
        //         'edit' => [],
        //         'delete' => [],
        //     ],
        // ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->with('order.pageTitle.detail');
        $show->add('client', null, [
            'label' => 'order.field.client',
            'associated_property' => 'name',
        ]);
        $show->add('orderDate', 'datetime', [
            'label' => 'order.field.orderDate',
            'format' => 'd.m.Y H:i',
        ]);
        $show->add('status', null, [
            'label' => 'order.field.status',
        ]);
        $show->end();

        $show->with('order.pageTitle.detail');
        $show->add('totalAmount', 'currency', [
            'label' => 'order.field.totalAmount',
            'currency' => 'RUB',
        ]);
        $show->end();

        $show->with('order.pageTitle.detail');
        $show->add('weight', null, [
            'label' => 'order.field.weight',
        ]);
        $show->add('volume', null, [
            'label' => 'order.field.volume',
        ]);
        $show->end();

        $show->with('order.pageTitle.detail');
        $show->add('createdAt', 'datetime', [
            'label' => 'order.field.createdAt',
            'format' => 'd.m.Y H:i',
        ]);
        $show->add('updatedAt', 'datetime', [
            'label' => 'order.field.updatedAt',
            'format' => 'd.m.Y H:i',
        ]);
        $show->end();
    }

    public function toString(object $object): string
    {
        return $object instanceof Order
            ? (string) $object
            : $this->trans('order.entity.singular');
    }
}
