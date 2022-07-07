<?php

namespace App\Controller;

use App\Entity\ModePaiement;
use App\Entity\Compte;
use App\Entity\DataApi;
use App\Form\ModePaiementFormType;
use App\Form\CompteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ModePaiementController extends AbstractController
{
    #[Route('/modepaiement/add', name: 'modepaiement_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $modepaiement = new ModePaiement();
        $form = $this->createForm(ModePaiementFormType::class, $modepaiement, [
            'action' => $this->generateUrl('modepaiement_add'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $modepaiement->setProprietaire($this->getUser());

            $entityManager->persist($modepaiement);
            $entityManager->flush();

            return $this->redirectToRoute('modepaiement_list');
        }

        return $this->render('forms/modepaiement.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/modepaiement/modify/{id}', name: 'modepaiement_modify')]
    public function modify(ModePaiement $modepaiement, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($modepaiement->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce mode de paiement est inconnu');
        }
        $form = $this->createForm(ModePaiementFormType::class, $modepaiement, [
            'action' => $this->generateUrl('modepaiement_modify', ['id' => $modepaiement->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($modepaiement);
            $entityManager->flush();

            return $this->redirectToRoute('modepaiement_list');
        }
        
        return $this->render('forms/modepaiement.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/modepaiement/list', name: 'modepaiement_list')]
    public function list(): Response
    {
        $compteForm = $this->createForm(CompteFormType::class, new Compte());

        return $this->render('modepaiement.html.twig', [
            'modepaiements' => $this->getUser()->getModePaiements(),
            'compteForm' => $compteForm->createView()
        ]);
    }

    #[Route('/modepaiement/select2', name: 'modepaiement_select2')]
    public function select2(): Response
    {
        $retour = new DataApi($this->getUser()->getModePaiementsApi(), true);
        return new JsonResponse($retour);  
    }
}
