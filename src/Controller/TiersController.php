<?php

namespace App\Controller;

use App\Entity\Tiers;
use App\Entity\Compte;
use App\Entity\DataApi;
use App\Form\TiersFormType;
use App\Form\CompteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TiersController extends AbstractController
{
    #[Route('/tiers/add', name: 'tiers_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tiers = new Tiers();
        $form = $this->createForm(TiersFormType::class, $tiers, [
            'action' => $this->generateUrl('tiers_add'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tiers->setProprietaire($this->getUser());

            $entityManager->persist($tiers);
            $entityManager->flush();

            return $this->redirectToRoute('tiers_list');
        }

        return $this->render('forms/tiers.html.twig', [
            'form' => $form->createView()
        ]);   
    }

    #[Route('/tiers/modify/{id}', name: 'tiers_modify')]
    public function modify(Tiers $tiers, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($tiers->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce tiers est inconnu');
        }
        $form = $this->createForm(TiersFormType::class, $tiers, [
            'action' => $this->generateUrl('tiers_modify', ['id' => $tiers->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tiers);
            $entityManager->flush();

            return $this->redirectToRoute('tiers_list');
        }

        return $this->render('forms/tiers.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tiers/list', name: 'tiers_list')]
    public function list(): Response
    {
        $tiersForm = $this->createForm(TiersFormType::class, new Tiers());
        $compteForm = $this->createForm(CompteFormType::class, new Compte());

        return $this->render('tiers.html.twig', [
            'tiers' => $this->getUser()->getTiers(),
            'compteForm' => $compteForm->createView(),
            'tiersForm' => $tiersForm->createView()
        ]);
    }

    #[Route('/tiers/select2', name: 'tiers_select2')]
    public function select2(): Response
    {
        $retour = new DataApi($this->getUser()->getTiersApi(), true);
        return new JsonResponse($retour);  
    }
}
