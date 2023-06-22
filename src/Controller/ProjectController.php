<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Parts;
use App\Entity\Project;
use App\Form\NoteType;
use App\Form\PartsType;
use App\Form\ProjectType;
use App\Repository\TaskRepository;
use App\Repository\PartsRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\NoteService;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/project/list', name: 'app_project_list')]
    public function index(
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
    ): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findBy(['owner' => $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()])]),
            'title' => 'Liste des projet',
        ]);
    }

    #[Route('/project/{project}', name: 'app_project_one', requirements: ['project' => '\d+'])]
    public function one(
        Project        $project,
        TaskRepository $noteRepository,
    ): Response
    {
        if ($this->getUser()->getUserIdentifier() === $project->getOwner()->getUserIdentifier()){
            $todo = $noteRepository->findBy(['project' => $project, 'finishAt' => null]);
            $query = $noteRepository->createQueryBuilder('n')
                ->where('n.project = :project')
                ->andWhere('n.finishAt IS NOT NULL')
                ->setParameter('project', $project)
                ->getQuery();

            $doing = $query->getResult();
        } else {
            $this->addFlash('danger', 'Vous ne pouvez pas accéder a la liste');
            return $this->redirectToRoute('app_project_list');
        }

        return $this->render('task/index.html.twig', [
            'title' => $project->getName(),
            'todo' => $todo,
            'doing' => $doing,
            'id' => $project->getId()
        ]);
    }

    #[Route('/project/create', name: 'app_project_create')]
    public function create(
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        Request $request
    ): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $project->setOwner($userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]));
                $manager->persist($project);
                $manager->flush();
                $this->addFlash('success', 'Le projet a bien été créer');

                return $this->redirectToRoute('app_project_one', ['project' => $project->getId()]);
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
            }
        }

        return $this->render('project/form.html.twig', [
            'title' => 'Créer un projet',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/project/edit/{project}', name: 'app_project_edit', requirements: ['project' => '\d+'])]
    public function edit(
        Project $project,
        EntityManagerInterface $manager,
        Request $request
    ): Response
    {

        if ($project->getOwner()->getUserIdentifier() === $this->getUser()->getUserIdentifier()){
            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $manager->flush();
                    $this->addFlash('success', 'Le Projet a bine été modifié');

                    return $this->redirectToRoute('app_project_one', ['project' => $project->getId()]);
                } catch (\Exception $exception) {
                    $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
                }
            }
        } else{
            $this->addFlash('danger', 'Vous ne pouvez pas accéder a la liste');
            return $this->redirectToRoute('app_project_list');
        }

        return $this->render('project/form.html.twig', [
            'title' => "Modifier un projet",
            'form' => $form->createView(),
        ]);
    }


    #[Route('/project/delete/{project}', name: 'app_project_delete')]
    public function delete(
        Project           $project,
        ProjectRepository $projectRepository,
        Request        $request
    ): Response
    {
        if ($project->getOwner()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()){
            $this->addFlash('danger', 'Vous ne pouvez pas accéder à ce projet');
            return  $this->redirectToRoute('app_homepage');
        }
        try {
            $projectRepository->remove($project, true);
            $this->addFlash('success', 'La suppression a été réalisé avec succès');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
        }

        return $this->redirectToRoute('app_project_list');
    }
}
