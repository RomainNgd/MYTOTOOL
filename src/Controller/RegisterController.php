<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManager): Response
    {
        $notification = null;
        $user = New User();
        $registerForm = $this->createForm(RegisterType::class, $user);
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()){
            $user = $registerForm->getData();
            $searchEmail = $entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            if (!$searchEmail){
                $user->setRoles(['ROLE_USER']);
                $password = $encoder->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('register_success', 'Vous êtes bien inscit vous pouvez vous connecter');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('danger','cette email est déjà utilisé');
            }
        }
        return $this->render('register/index.html.twig', [
            'registerForm' => $registerForm,
        ]);
    }
}
