<?php

namespace App\Controller;

use App\Classe\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

    /** Supprimer le panier entier
     * @Route("/cart/remove", name="remove-my-cart")
     */
    public function remove(Cart $cart): Response
    {   
        $cart->remove();

        return $this->redirectToRoute('products');
    }

    /** Supprimer un produit
     * @Route("/cart/delete/{id}", name="delete-to-cart")
     */
    public function delete(Cart $cart, $id): Response
    {   
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }

    /** Diminuer la quantité d'un produit
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     */
    public function decrease(Cart $cart, $id): Response
    {   
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }
}
