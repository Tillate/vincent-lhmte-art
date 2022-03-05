<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $notification = null;
        $notificationError = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On récupère la data du formulaire
            $user = $form->getData();

            // On verifie si l'email existe déjà dans la base de donnée
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail()); 
            
            //Si l'email n'existe pas encore on continue
            if (!$search_email) {
                //On récupère le password saisie avec la fonction getPassword() puis on utilise la fonction hashPassword pour le crypter
                $password = $passwordHasher->hashPassword($user,$user->getPassword()); 

                $user->setPassword($password); //On set le password 
    
                $this->entityManager = $doctrine->getManager();
                $this->entityManager->persist($user); //On fige la donnée avant le flush
                $this->entityManager->flush(); //On flush la donnée dans la base de données

                $mail = new Mail();
                $content = "Bonjour <b>".$user->getFirstName()." ".$user->getLastName()."</b>,"."
                <br><br>Bienvenue sur Vincent LHMTE Art.
                <br><br>Votre inscription s'est correctement déroulée. Vous pouvez-désormais accéder à votre compte en utilisant vos identifiants.";
                $mail->send($user->getEmail(), $user->getFirstName(), "Confirmation d'inscription - Vincent LHMTE Art", $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            } else {
                $notificationError = "L'email que vous avez renseigné existe déjà.";
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
            'notificationError' => $notificationError
        ]);
    }
}

