<?php

namespace App\Controller\Admin;

use App\Entity\Vehicle\Truck;
use App\Enum\EngineType;
use App\Service\Vehicle\TruckService;
use Doctrine\ORM\EntityManagerInterface;
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

class TruckCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TruckService $truckService,
    ) {}

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'truck.pageTitle.index')
            ->setPageTitle(Crud::PAGE_DETAIL, 'truck.pageTitle.detail')
            ->setPageTitle(Crud::PAGE_NEW, 'truck.pageTitle.new')
            ->setPageTitle(Crud::PAGE_EDIT, 'truck.pageTitle.edit')
            ->setEntityLabelInSingular('truck.entity.singular')
            ->setEntityLabelInPlural('truck.entity.plural')
            ->setDefaultSort([
                'createdAt' => 'DESC',
            ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportAction = Action::new('export', 'truck.action.export', 'fa fa-download')
            ->linkToRoute('admin_truck_custom_export')
            ->createAsGlobalAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $exportAction)
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
                return $action->setLabel('truck.action.new');
            });
    }

    public static function getEntityFqcn(): string
    {
        return Truck::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return $this->truckService->createNew();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        assert($entityInstance instanceof Truck);

        $this->truckService->saveNew($entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        assert($entityInstance instanceof Truck);

        $this->truckService->update($entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        assert($entityInstance instanceof Truck);

        $this->truckService->delete($entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm()
                ->hideOnIndex(),

            TextField::new('vin', 'truck.field.vin'),
            TextField::new('brand', 'truck.field.brand'),
            TextField::new('model', 'truck.field.model'),
            TextField::new('licensePlate', 'truck.field.licensePlate'),

            DateField::new('manufactureDate', 'truck.field.manufactureDate'),
            DateField::new('purchaseDate', 'truck.field.purchaseDate'),

            ChoiceField::new('engineType', 'truck.field.engineType')
                ->setChoices(
                    array_combine(
                        array_map(
                            fn(EngineType $type) => "truck.engineType.{$type->value}",
                            EngineType::cases(),
                        ),
                        EngineType::cases(),
                    ),
                )
                ->renderAsBadges(),

            IntegerField::new('engineCapacity', 'truck.field.engineCapacity')
                ->hideOnIndex(),

            NumberField::new('engineVolume', 'truck.field.engineVolume')
                ->setNumDecimals(2)
                ->setNumberFormat('%.2f')
                ->hideOnIndex(),

            IntegerField::new('mileageInitial', 'truck.field.mileageInitial')
                ->hideOnIndex(),

            IntegerField::new('emptyWeight', 'truck.field.emptyWeight'),
            IntegerField::new('maxWeight', 'truck.field.maxWeight'),

            TextField::new('color', 'truck.field.color')
                ->hideOnIndex(),

            TextareaField::new('description', 'truck.field.description')
                ->hideOnIndex(),

            DateTimeField::new('createdAt', 'truck.field.createdAt')
                ->hideOnForm()
                ->setFormat('dd.MM.Y HH:mm')
                ->hideOnIndex(),

            DateTimeField::new('updatedAt', 'truck.field.updatedAt')
                ->hideOnForm()
                ->setFormat('dd.MM.Y HH:mm')
                ->hideOnIndex(),
        ];
    }
}
