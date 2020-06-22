<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     */
    public function index() :Response
    {
        return $this->render('default/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/my-profil", name="my_profil")
     */
    public function myProfil(): Response
    {
        return $this->render('wild/myProfil.html.twig');
    }
}