<?php

namespace App\Controller;

use App\Repository\TodoUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/todo')]
final class TodoController extends AbstractController
{
    #[Route('', name: 'todo_index')]
    public function index(TodoUserRepository $todoUserRepository): Response 
    {
        $user = $todoUserRepository->find(3);

        dd($user);
        if ($user) {
            $user->getTasks()->toArray(); 
        }

        return $this->render('todo/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/reading', name: 'todo_reading')]
    public function reading(): Response 
    {
        return $this->render('todo/reading.html.twig', [
            'controller_name' => 'TodoController',
        ]);
    }
}
