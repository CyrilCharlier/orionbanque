<?php

namespace App\Controller;

use App\Entity\Banque;
use App\Entity\Compte;
use App\Form\BanqueFormType;
use App\Form\CompteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BanqueController extends AbstractController
{
    #[Route('/banque/add', name: 'banque_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $banque = new Banque();
        $form = $this->createForm(BanqueFormType::class, $banque, [
            'action' => $this->generateUrl('banque_add'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $banque->setProprietaire($this->getUser());

            $entityManager->persist($banque);
            $entityManager->flush();

            return $this->redirectToRoute('banque_list');
        }

        return $this->render('forms/banque.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/banque/modify/{id}', name: 'banque_modify')]
    public function modify(Banque $banque, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($banque->getProprietaire() !== $this->getUser()) {
            throw new AccessDeniedException('Cette banque est inconnue');
        }
        $form = $this->createForm(BanqueFormType::class, $banque, [
            'action' => $this->generateUrl('banque_modify', ['id' => $banque->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($banque);
            $entityManager->flush();

            return $this->redirectToRoute('banque_list');
        }

        return $this->render('forms/banque.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/banque/list', name: 'banque_list')]
    public function list(): Response
    {
        return $this->render('banque.html.twig', [
            'banques' => $this->getUser()->getBanques(),
        ]);
    }
}
