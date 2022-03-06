<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        //Fonctionnalité mise en avant produit
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        //Fonctionnalité article a la une
        $headers = $this->entityManager->getRepository(Header::class)->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'headers' => $headers
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions")
     */
    public function mentions(): Response
    {
        return $this->render('home/mentions.html.twig');
    }

    /**
     * @Route("/cgv", name="cgv")
     */
    public function cgv(): Response
    {
        return $this->render('home/cgv.html.twig');
    }
}
