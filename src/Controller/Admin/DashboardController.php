<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Expense\ExpenseCategory;
use App\Entity\Order;
use App\Entity\Vehicle\Truck;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private const string TRANSLATION_DOMAIN = 'easyAdmin';

    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect(
            $adminUrlGenerator->setController(ExpenseCategoryCrudController::class)->generateUrl(),
        );

        // return parent::index();
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain(self::TRANSLATION_DOMAIN)
            ->setTitle('LogiTruck'); // toDo set from .env
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('dashboard.menu.dashboard', 'fa fa-home');
        yield MenuItem::section('dashboard.menu.management');
        yield MenuItem::linkToCrud('dashboard.menu.expense_categories', 'fas fa-tags', ExpenseCategory::class);
        yield MenuItem::linkToCrud('dashboard.menu.trucks', 'fas fa-truck', Truck::class);
        yield MenuItem::linkToCrud('dashboard.menu.clients', 'fas fa-users', Client::class);
        yield MenuItem::linkToCrud('dashboard.menu.orders', 'fas fa-shopping-cart', Order::class);
    }
}
