<?php
//src/Controller/WildController

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index() :Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }
    /**
     * @Route("/wild/show/{page}", name="wild_show")
     */
    public function show(int $page): Response
    {
        return $this->render('wild/show.html.twig', ['page' =>$page]);
    }
}