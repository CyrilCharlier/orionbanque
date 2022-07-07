<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\DataApi;
use App\Entity\Operation;
use App\Form\CompteFormType;
use App\Form\OperationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CompteController extends AbstractController
{
    #[Route('/compte/add', name: 'compte_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteFormType::class, $compte, [
            'action' => $this->generateUrl('compte_add'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte->setProprietaire($this->getUser());

            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('compte_list');
        }

        return $this->render('forms/compte.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/compte/modify/{id}', name: 'compte_modify')]
    public function mod(Compte $compte, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompteFormType::class, $compte, [
            'action' => $this->generateUrl('compte_modify', ['id' => $compte->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte->setProprietaire($this->getUser());

            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('compte_list');
        }

        return $this->render('forms/compte.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/compte/list', name: 'compte_list')]
    public function list(): Response
    {
        return $this->render('compte.html.twig', [
            'comptes' => $this->getUser()->getComptes(),
        ]);
    }

    #[Route('/compte/show-table/{id}/{slug}', name: 'compte_show_table')]
    public function showTable(Compte $compte): Response
    {
        if($compte->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce compte est inconnu');
        }
        
        $compteForm = $this->createForm(CompteFormType::class, $compte);

        $operation = new Operation();
        $operationForm = $this->createForm(OperationFormType::class, $operation);

        return $this->render('compte-show-table.html.twig', [
            'compteForm' => $compteForm->createView(),
            'operationForm' => $operationForm->createView(),
            'compte' => $compte,
        ]);
    }

    #[Route('/compte/{id}/table', name: 'compte_table')]
    public function jsonTable(Compte $compte): Response
    {
        if($compte->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce compte est inconnu');
        }
        
        $retour = new DataApi($compte->getData(), true);
        return new JsonResponse($retour);
    }
}
