<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Order;
use App\Enum\OrderStatus;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        EntityManagerInterface $entityManager,
        ClientRepository $clientRepository,
        TranslatorInterface $translator,
    ): Response {

        //        dd(($translator->trans("orderStatus.new", domain: 'orders')));
        //        dd(t("orderStatus.new", [], 'orders'));

        $result = $translator->trans("test");
        dump($result); // Выведет "Дизельный"
        die;
        try {
            $message = t("engineType.diesel", domain: 'orders');
            dump($message);
            dump($message->getMessage()); // Должно быть "engineType.diesel"
            dump($message->getDomain());  // Должно быть "order"
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        //        echo t("engineType.diesel", domain: 'order')->getMessage();
        die();
        $client = $clientRepository->find('0199a3a4-b4c6-75d8-931b-1f3db3fdbddf');
        //        dd($client);


        $order = new Order(Uuid::v7());
        $order->orderDate = new \DateTimeImmutable();
        $order->status = OrderStatus::NEW;
        $order->client = $client;

        //        dd($order);
        $entityManager->persist($order);
        $entityManager->flush();
        dd($order);


        //        $client = new Client(Uuid::v7());
        //        $client->name = 'Название компании';
        //        $client->contactPerson = 'Иванов Иван';
        //        $client->phone = '+79991234567';
        //        $client->email = 'client@example.com';
        //        $client->taxNumber = '1234567890';
        //
        ////        dd($user);
        //        $entityManager->persist($client);
        //        $entityManager->flush();
        //        dd($client);

        //        $activeUsers = $userRepository->findActiveUsers();
        //        $firstUser = $activeUsers[0];
        //        /** @var User $firstUser */
        //        $firstUser->setStatus(UserStatus::BANNED);
        //        $entityManager->persist($firstUser);
        //        $entityManager->flush();
        //
        //        dd($firstUser);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/steven', name: 'app_steven')]
    public function steveAction(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'StevenController',
        ]);
    }

    #[Route('/client/{id}', name: 'client_info')]
    public function clientInfo(Client $client, EntityManagerInterface $entityManager): Response
    {
        $client->contactPerson = $client->contactPerson . '1';
        $entityManager->persist($client);
        $entityManager->flush();
        dd($client);
        //        return $this->render('home/index.html.twig', [
        //            'controller_name' => 'StevenController',
        //        ]);
    }
}
