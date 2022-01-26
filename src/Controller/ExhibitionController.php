<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExhibitionController extends AbstractController
{
    /**
     * @Route("/expositions", name="exhibitions")
     */
    public function index(): Response
    {
        return $this->render('exhibition/index.html.twig');
    }
}
