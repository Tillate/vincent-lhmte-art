<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart
{
    private $session;

    public function __construct (EntityManagerInterface $entityManager,SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    //AJOUTER UNE QUANTITE AU PANIER
    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id]=1;
        }


        $this->session->set('cart',$cart);
    }

    //AFFICHER LE PANIER
    public function get()
    {
        return $this->session->get('cart');
    }

    //SUPPRIMER LE PANIER
    public function remove()
    {
        return $this->session->remove('cart');
    }

    //SUPPRIMER UN PRODUIT DU PANIER
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart',$cart);
    }

    //DIMINUER LA QUANTITE DANS LE PANIER
    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);

        if ($cart[$id] > 1) {
            //retirer une quantité
            $cart[$id]--;
        } else {
            //diminuer la quantité
            unset($cart[$id]);
        }

        return $this->session->set('cart',$cart);
    }

    public function getFull()
    {
        $cartComplete = [];

        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                
                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }
}