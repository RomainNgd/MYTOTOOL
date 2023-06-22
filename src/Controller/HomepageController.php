<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        TaskRepository    $noteRepository,
        ProjectRepository $projectRepository,
        UserRepository    $userRepository,
    ): Response
    {
        $tasks = $noteRepository->findBy(['user'=> $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()])]);
        $projects = $projectRepository->findBy(['owner'=> $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()])]);
        return $this->render('homepage/index.html.twig', [
            'current_menu'=>'accueil',
            'tasks' => $tasks,
            'projects' => $projects,
            'title' => 'Accueil'
        ]);
    }
}
