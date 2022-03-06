<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');

        }

        //Envoyer un email à l'utilisateur pour lui indiquer l'echec de paiement
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFirstName()." ".$order->getUser()->getLastName().",
        <br><br> Votre commande a échoué. Le moyen de paiement utilisé ne semble pas avoir fonctionné.
        <br><br> Vous ne serez donc pas prélevé et votre commande ne sera pas validée.
        <br><br> Vous pouvez donc réésayer votre commande à nouveau.";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Commande non validé - Erreur de paiement -  Vincent LHMTE Art.', $content);
        

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
