<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Form\TaskType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $manager,
    ): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['email' => $email]);
        $form = $this->createForm(ProfilType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if($userRepository->findOneBy(['email' => $form->get('email')->getData()])->getEmail() !== $email){
                    $this->addFlash('danger', 'Adresse mail dèjà utilisé');

                } else {
                    if (password_verify($form->get('last')->getData(), $user->getPassword())){
                        $user->setPassword(password_hash($form->get('password')->getData(), PASSWORD_DEFAULT));
                        $user->setEmail($form->get('email')->getData());
                        $manager->flush();
                        $this->addFlash('success', 'Le profil a bien été modifié');
                    } else{
                        $this->addFlash('danger', 'Mot de passe éroné');
                    }
                }

            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Une erreur c\'est produit veuillez contacter le support');
            }
        }
        return $this->render('profil/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
