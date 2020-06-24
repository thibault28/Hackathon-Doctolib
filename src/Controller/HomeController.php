<?php

namespace App\Controller;

use App\Service\Chars;
use App\Service\Medecins;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {

        $medecins = new Medecins();
        $chars = new Chars();

        //$medecins = $medecins->getMedecins(strtoupper($chars->removeAccent('rivoal')));
        //var_dump($medecins);

        return $this->render('home/index.html.twig', []);
    }
}
