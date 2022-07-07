<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteFormType::class, $compte);
        $form->handleRequest($request);

        return $this->render('home.html.twig', [
            'compteForm' => $form->createView(),
        ]);
    }
}
