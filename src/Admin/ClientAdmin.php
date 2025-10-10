<?php

namespace App\Admin;

use App\Entity\Client;
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
    protected function configure(): void
    {
        $this->setTranslationDomain('sonataAdmin');
    }

    protected function createNewInstance(): object
    {
        return new Client(Uuid::v7());
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->with('client.pageTitle.edit');
        $form->add('name', TextType::class, [
            'label' => 'client.field.name',
        ]);
        $form->add('contactPerson', TextType::class, [
            'label' => 'client.field.contactPerson',
        ]);
        $form->add('phone', TextType::class, [
            'label' => 'client.field.phone',
        ]);
        $form->add('email', EmailType::class, [
            'label' => 'client.field.email',
        ]);
        $form->end();

        $form->with('client.pageTitle.edit');
        $form->add('taxNumber', TextType::class, [
            'label' => 'client.field.taxNumber',
        ]);
        $form->add('address', TextareaType::class, [
            'label' => 'client.field.address',
            'required' => false,
            'attr' => [
                'rows' => 3,
            ],
        ]);
        $form->end();

        $form->with('client.pageTitle.edit');
        $form->add('paymentTerms', TextareaType::class, [
            'label' => 'client.field.paymentTerms',
            'required' => false,
            'attr' => [
                'rows' => 3,
            ],
        ]);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name', null, [
            'label' => 'client.field.name',
        ]);
        $datagrid->add('contactPerson', null, [
            'label' => 'client.field.contactPerson',
        ]);
        $datagrid->add('phone', null, [
            'label' => 'client.field.phone',
        ]);
        $datagrid->add('email', null, [
            'label' => 'client.field.email',
        ]);
        $datagrid->add('taxNumber', null, [
            'label' => 'client.field.taxNumber',
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name', null, [
            'label' => 'client.field.name',
        ]);
        $list->add('contactPerson', null, [
            'label' => 'client.field.contactPerson',
        ]);
        $list->add('phone', null, [
            'label' => 'client.field.phone',
        ]);
        $list->add('email', null, [
            'label' => 'client.field.email',
        ]);
        $list->add('taxNumber', null, [
            'label' => 'client.field.taxNumber',
        ]);
        $list->add('createdAt', 'datetime', [
            'label' => 'client.field.createdAt',
            'format' => 'd.m.Y H:i',
        ]);
        $list->add('updatedAt', 'datetime', [
            'label' => 'client.field.updatedAt',
            'format' => 'd.m.Y H:i',
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
        $show->with('client.pageTitle.detail', ['class' => 'col-md-6']);
        $show->add('name', null, [
            'label' => 'client.field.name',
        ]);
        $show->add('contactPerson', null, [
            'label' => 'client.field.contactPerson',
        ]);
        $show->add('phone', null, [
            'label' => 'client.field.phone',
        ]);
        $show->add('email', null, [
            'label' => 'client.field.email',
        ]);
        $show->end();

        $show->with('client.pageTitle.detail');
        $show->add('taxNumber', null, [
            'label' => 'client.field.taxNumber',
        ]);
        $show->add('address', null, [
            'label' => 'client.field.address',
        ]);
        $show->end();

        $show->with('client.pageTitle.detail');
        $show->add('paymentTerms', null, [
            'label' => 'client.field.paymentTerms',
        ]);
        $show->add('createdAt', 'datetime', [
            'label' => 'client.field.createdAt',
            'format' => 'd.m.Y H:i',
        ]);
        $show->add('updatedAt', 'datetime', [
            'label' => 'client.field.updatedAt',
            'format' => 'd.m.Y H:i',
        ]);
        $show->end();

        $show->with('client.field.orders', ['class' => 'col-md-6']);
        $show->add('orders', null, [
            'label' => 'client.field.orders',
            'associated_property' => fn($order) => $order->getId(),
        ]);
        $show->end();
    }

    public function toString(object $object): string
    {
        return $object instanceof Client
            ? $object->name ?? ''
            : $this->trans('client.entity.singular');
    }
}
