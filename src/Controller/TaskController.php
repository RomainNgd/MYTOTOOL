<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Parts;
use App\Entity\Project;
use App\Form\NoteType;
use App\Form\PartsType;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\NoteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/task/create/{project}', name: 'app_task_create',requirements: ['project' => '\d+'])]
    public function create(
        Project $project,
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        Request $request
    ): Response
    {
        if ($project->getOwner()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()){
            $this->addFlash('danger', 'Vous ne pouvez pas accéder à cette tâche');
            return  $this->redirectToRoute('app_homepage');
        }
        $note = new Task();
        $form = $this->createForm(TaskType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $note->setProject($project);
                $note->setCreateAt(new \DateTimeImmutable());
                if (!$note->getUser()){
                    $note->setUser($userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]));
                }
                $manager->persist($note);
                $manager->flush();
                $this->addFlash('success', 'Vous ne pouvez pas créer de tâche pour ce projet');

                return $this->redirectToRoute('app_project_one', ['project' => $project->getId()]);
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
            }
        }

        return $this->render('task/create.html.twig', [
            'title' => 'CRéation d\'une tache',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/task/edit/{task}', name: 'app_task_edit')]
    public function edit(
        Task                   $task,
        EntityManagerInterface $manager,
        Request                $request
    ): Response
    {
        if ($task->getUser()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()){
            $this->addFlash('danger', 'Vous ne pouvez pas accéder à cette tâche');
            return  $this->redirectToRoute('app_homepage');
        }
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $manager->flush();
                $this->addFlash('success', 'La tache a bien été modifié');

                return $this->redirectToRoute('app_project_one', ['project' => $task->getProject()->getId()]);
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
            }
        }

        return $this->render('task/create.html.twig', [
            'title' => 'edition d\'une tache',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/task/status/{task}', name: 'app_task_status')]
    public function status(
        Task                   $task,
        EntityManagerInterface $manager,
        Request                $request
    ): Response
    {
        if ($task->getUser()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()){
            $this->addFlash('danger', 'Vous ne pouvez pas accéder à cette tâche');
            return  $this->redirectToRoute('app_homepage');
        }
        if ($task->getFinishAt()){
            $task->setFinishAt(null);
        } else {
            $task->setFinishAt(new \DateTimeImmutable());
        }
        try {
            $manager->flush();
            $this->addFlash('success', 'La status de la tâche a bien été changé');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
        }

        return $this->redirectToRoute('app_project_one',['project' => $task->getProject()->getId()]);
    }

    #[Route('/task/delete/{task}', name: 'app_task_delete')]
    public function delete(
        Task           $task,
        TaskRepository $noteRepository,
        Request        $request
    ): Response
    {
        if ($task->getUser()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()){
            $this->addFlash('danger', 'Vous ne pouvez pas accéder à cette tâche');
            return  $this->redirectToRoute('app_homepage');
        }
        try {
            $noteRepository->remove($task, true);
            $this->addFlash('success', 'La suppression a été réalisé avec succès');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
        }

        return $this->redirectToRoute('app_project_one',['project' => $task->getProject()->getId()]);
    }
}
