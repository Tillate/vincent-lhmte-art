<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $entityManager;
    //Pour récupérer le formulaire de changement mdp
    /**
     * AccountPasswordController constructor
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
            $this->entityManager = $entityManager;
    }
    

    /**
     * @Route("/compte/modifier-mon-mot-de-passe", name="account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = null;
        $notificationError = null;

        $user = $this->getUser();
        $form=$this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();

            //On teste si l'ancien mdp est valide
            if ($passwordHasher->isPasswordValid($user, $old_password)) {
                
                //Si ancien mdp valide on get le nouveau dans le form, on le hash, set puis flush
                $user = $form->getData();
                $new_password = $form->get('new_password')->getData();
                $password = $passwordHasher->hashPassword($user, $new_password);

                $user->setPassword($password);
                $this->entityManager->flush();
                $notification = "Votre mot de passe a été mis à jour.";

            } else {
                $notificationError = "Le mot de passe saisi est invalide.";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
            'notificationError' => $notificationError,
        ]);
    }
}
