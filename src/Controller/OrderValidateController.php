<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart,$stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($order->getState()== 0) {
            //Vider la session "cart"
            $cart->remove();


            //Modifier le statut de la commande en mettant 1
            $order->setState(1);
            $this->entityManager->flush();

            //Envoyer un email de confirmation de commande au client
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstName()."<br/>Bienvenue sur Ma Boutique de mode. <br><br/> Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita, quis?";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande sur Ma Boutique est bien validée.', $content);
        }
        //Afficher les information de la commande de l'utilisateur

        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}
