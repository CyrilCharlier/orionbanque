<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\DataApi;
use App\Entity\Operation;
use App\Form\OperationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OperationController extends AbstractController
{
    #[Route('/operation/{id}', name: 'operation_get')]
    public function get(Operation $operation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($operation->getCompte()->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce compte est inconnu');
        }
        $data = [];
        $data[] = $operation->getOperationApi();
        $retour = new DataApi($data, true);
        return new JsonResponse($retour);
    }

    #[Route('/operation/add/compte/{id}', name: 'operation_add')]
    public function add(Compte $compte, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($compte->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce compte est inconnu');
        }

        $operation = new Operation();
        $form = $this->createForm(OperationFormType::class, $operation, [
            'action' => $this->generateUrl('operation_add', ['id' => $compte->getId()]),
            'method' => 'POST',
        ])
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $operation->setCompte($compte);

            $entityManager->persist($operation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compte_show_table', ['id' => $compte->getId(), 'slug' => $compte->getSlug()]);
    }

    #[Route('/operation/modify/{id}', name: 'operation_mod')]
    public function mod(Operation $operation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($operation->getCompte()->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce compte est inconnu');
        }

        $form = $this->createForm(OperationFormType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($operation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compte_show_table', ['id' => $operation->getCompte()->getId(), 'slug' => $operation->getCompte()->getSlug()]);
    }

    #[Route('/operation/pointe/{id}', name: 'operation_pointe')]
    public function point(Operation $operation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($operation->getCompte()->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Ce compte est inconnu');
        }

        $operation->setPointe(true);
        $entityManager->persist($operation);
        $entityManager->flush();

        $retour = new DataApi([], true);
        return new JsonResponse($retour);
    }
}
