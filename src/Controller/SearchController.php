<?php 

namespace App\Controller;

use App\Service\Medecins;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function cleanString($string)
    {
        $string = strtolower($string);
        $string = preg_replace("/[^a-z0-9_'\s-]/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", " ", $string);
        return $string;
    }


    /**
     * @Route("/search", name="search")
     */
    public function search()
    {
        if (isset($_GET['search']) && !empty($_GET['search']))
        {
            $search = urlencode($this->cleanString($_GET['search']));
            $medecin = new Medecins();
            $medecin = $medecin->getSearch($_GET['search']);

        }

        return $this->render('search/search.html.twig',[
            'medecins'=>$medecin,
        ]);
    }
}