<?php

namespace App\Form\Vehicle;

use App\Entity\Vehicle\Truck;
use App\Enum\EngineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TruckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vin')
            ->add('brand')
            ->add('model')
            ->add('manufactureDate', null, [
                'widget' => 'single_text',
            ])
            ->add('mileageInitial')
            ->add('engineType', EnumType::class, [
                'class' => EngineType::class,
                'choice_label' => function (EngineType $engineType) {
                    return $engineType->getLabel();
                },
                'placeholder' => 'Выберите тип двигателя',
            ])
            ->add('engineCapacity')
            ->add('engineVolume')
            ->add('purchaseDate', null, [
                'widget' => 'single_text',
            ])
            ->add('color')
            ->add('licensePlate')
            ->add('maxWeight')
            ->add('emptyWeight')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Truck::class,
        ]);
    }
}
