<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart
{
    //Mise en place du constructeur pour la création de session & récupérer les products
    private $session;

    public function __construct (EntityManagerInterface $entityManager,SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    //Fonction AJOUTER UNE QUANTITE AU PANIER
    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        //SI produit deja en panier on incremente de 1
        //Sinon on ajoute $cart[id] 
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id]=1;
        }

        //On set les produits ajoutés
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
        $cart = $this->session->get('cart', []); //récupère le contenu du panier

        unset($cart[$id]); //Supprime le produit du panier

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

    //Récupérer les informations produits dans le panier
    public function getFull()
    {
        //On initialise un tableau dans lequel on stockera l'ensemble des produits du panier
        $cartComplete = [];

        //On get le contenu du panier
        if ($this->get()) {
            //Boucle for each pour récupérer chaque id et quantité dans le panier
            foreach ($this->get() as $id => $quantity) {
                //On recupere entityManager dans le constructor pour accéder a la class Product
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                
                //Si le product object n'existe pas on supprime
                if (!$product_object) {
                    $this->delete($id);
                    continue; // pour sortir de la boucle
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