<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\PasswordLostType;
use App\Form\PasswordType;
use App\Form\ProjectType;
use App\Repository\UserRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    #[Route('/login/passwordlost', name: 'app_password')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        TokenService $tokenService,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(PasswordLostType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user instanceof User){
                $user->setResetkey($tokenService->generateToken());
                $entityManager->flush();
                return $this->redirectToRoute('app_password_reset', ['key' => $user->getResetkey()]);
            } else {
                $this->addFlash('success', 'Un mail a été envoyé a votre addresse mail si elle est correcte');
            }
        }
        return $this->render('password/index.html.twig', [
            'title' => 'Mot de passe oublié',
            'form' => $form->createView()
        ]);
    }


    #[Route('/login/password/{key}', name: 'app_password_reset')]
    public function create(
        string $key,
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        Request $request
    ): Response
    {
        $array = $userRepository->findAll();
        foreach ( $array as $users){
            if ($users->getResetkey() === $key){
                $user = $users;
            }
        }
        if (isset($user) && $user instanceof User){
            $form = $this->createForm(PasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $password = password_hash($form->get('password')->getData(), PASSWORD_DEFAULT);
                    $user->setPassword($password);
                    $manager->flush();
                    $this->addFlash('success', 'Le mot de passe a été modifié');
                    return $this->redirectToRoute('app_login');
                } catch (\Exception $exception) {
                    $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
                }
            }

            return $this->render('password/reset.html.twig', [
                'title' => 'Réinitilisé le mot de passe',
                'form' => $form
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
