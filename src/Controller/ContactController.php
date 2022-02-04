<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/me-contacter", name="contact")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice', "Merci de m'avoir contacté. Je vous répondrai dans les meilleures délais.");
            

            //Récupérer la data du formulaire 
            $form->getData();
            
            // Envoyer un mail recapitulatif du formulaire à Admin
            $content = "Bonjour,<br>
            Vous avez reçus un message de <strong>".$form->getData()['prenom']." ".$form->getData()['nom']."
            </strong><br><br>Adresse email : <strong>".$form->getData()['email']."</strong>
            <br><br>Message :<br> ".$form->getData()['content']."</br></br>";             
            $mail = new Mail();
            $mail->send('allan.lelous@gmail.com', 'Admin - Vincent LHMTE Art', 'Demande de contact', $content);

            //Envoyer un mail de confirmation à User
            $mail = new Mail();
            $mail->send($form->getData()['email'], 'Vincent LHMTE Art',
            'Votre demande de contact sur Vincent LHMTE Art.', 
            'Votre demande de contact à bien été envoyé.<br> Je vous répondrai dans les meilleures délais.<br><br>
             Votre message :<br>'.$form->getData()['content']);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
