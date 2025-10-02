<?php

namespace App\Controller\Admin;

use App\Entity\Vehicle\Truck;
use App\Enum\EngineType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Uid\Uuid;

use function Symfony\Component\Translation\t;

class TruckCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Список грузовиков')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Просмотр информации грузовика')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавить грузовик')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать информацию грузовика')
            ->setDefaultSort([
                'createdAt' => 'DESC',
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportAction = Action::new('export', 'Export CSV', 'fa fa-download')
            ->linkToRoute('admin_truck_custom_export')
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $exportAction)
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

    public static function getEntityFqcn(): string
    {
        return Truck::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return new Truck(Uuid::v7());
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm()
                ->hideOnIndex(),

            TextField::new('vin', 'VIN'),
            TextField::new('brand', 'Марка'),
            TextField::new('model', 'Модель'),
            TextField::new('licensePlate', 'Гос. номер'),

            DateField::new('manufactureDate', 'Дата производства'),
            DateField::new('purchaseDate', 'Дата покупки'),

            ChoiceField::new('engineType', 'Тип двигателя')
                ->setChoices(
                    array_combine(
                        array_map(
                            fn(EngineType $type) => t("engineType.{$type->value}"),
                            EngineType::cases(),
                        ),
                        EngineType::cases(),
                    ),
                )
                ->renderAsBadges(),

            IntegerField::new('engineCapacity', 'Мощность двигателя (л.с.)')
                ->hideOnIndex(),

            NumberField::new('engineVolume', 'Объем двигателя (л)')
                ->setNumDecimals(2)
                ->setNumberFormat('%.2f')
                ->hideOnIndex(),

            IntegerField::new('mileageInitial', 'Начальный пробег')
                ->hideOnIndex(),

            IntegerField::new('emptyWeight', 'Снаряженная масса (кг)'),
            IntegerField::new('maxWeight', 'Макс. масса (кг)'),

            TextField::new('color', 'Цвет')
                ->hideOnIndex(),

            TextareaField::new('description', 'Описание')
                ->hideOnIndex(),

            DateTimeField::new('createdAt', 'Создан')
                ->hideOnForm()
                ->setFormat('dd.MM.Y HH:mm')
                ->hideOnIndex(),

            DateTimeField::new('updatedAt', 'Обновлен')
                ->hideOnForm()
                ->setFormat('dd.MM.Y HH:mm')
                ->hideOnIndex(),
        ];
    }
}
