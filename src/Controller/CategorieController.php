<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Compte;
use App\Entity\DataApi;
use App\Form\CategorieFormType;
use App\Form\CompteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CategorieController extends AbstractController
{
    #[Route('/categorie/add', name: 'categorie_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieFormType::class, $categorie, [
            'action' => $this->generateUrl('categorie_add'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setProprietaire($this->getUser());

            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_list');
        }

        return $this->render('forms/categorie.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/categorie/modify/{id}', name: 'categorie_modify')]
    public function modify(Categorie $categorie, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($categorie->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Cette catégorie est inconnue');
        }
        $form = $this->createForm(CategorieFormType::class, $categorie, [
            'action' => $this->generateUrl('categorie_modify', ['id' => $categorie->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_list');
        }

        return $this->render('forms/categorie.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/categorie/list', name: 'categorie_list')]
    public function list(): Response
    {
        $compteForm = $this->createForm(CompteFormType::class, new Compte());

        return $this->render('categorie.html.twig', [
            'categories' => $this->getUser()->getCategories(),
            'compteForm' => $compteForm->createView(),
        ]);
    }

    #[Route('/categorie/select2', name: 'categorie_select2')]
    public function select2(): Response
    {
        $retour = new DataApi($this->getUser()->getCategorieApi(), true);
        return new JsonResponse($retour);  
    }
}
