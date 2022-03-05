<?php

namespace App\Controller;

use App\Classe\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    // private $entityManager;

    // public function __construct(EntityManagerInterface $entityManager)
    // {
    //     $this->entityManager = $entityManager;
    // }

    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart'=> $cart->getFull()
        ]);
    }

    /** Ajouter un produit au panier
     * @Route("/cart/add/{id}", name="add-to-cart")
     */
    public function add(Cart $cart,$id): Response
    {   
        $cart->add($id); //Appel de la fonction add($id) dans la classe cart

        return $this->redirectToRoute('cart');
    }

    /** Supprimer le panier entier
     * @Route("/cart/remove", name="remove-my-cart")
     */
    public function remove(Cart $cart): Response
    {   
        $cart->remove(); //Appel de la fonction remove() dans la classe cart

        return $this->redirectToRoute('products');
    }

    /** Supprimer un produit
     * @Route("/cart/delete/{id}", name="delete-to-cart")
     */
    public function delete(Cart $cart, $id): Response
    {   
        $cart->delete($id); //Appel de la fonction delete($id) dans la classe cart

        return $this->redirectToRoute('cart');
    }

    /** Diminuer la quantité d'un produit
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     */
    public function decrease(Cart $cart, $id): Response
    {   
        $cart->decrease($id); //Appel de la fonction decrease($id) dans la classe cart

        return $this->redirectToRoute('cart');
    }
}
