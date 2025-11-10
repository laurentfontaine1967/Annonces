<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\User;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/annonces')]
final class AnnoncesController extends AbstractController
{
    #[Route(name: 'app_annonces_index', methods: ['GET'])]
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('annonces/index.html.twig', [
            'annonces' => $annoncesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_annonces_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonces();

        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur connecté
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $annonce->setUser($user);

            // ⚠️ NE PAS manipuler le fichier manuellement ici.
            // Vich lit $annonce->getImageFile() (déjà alimenté par le FormType)
            // et mettra à jour imageName/imageSize lors du flush.

            $em->persist($annonce);
            $em->flush();

            $this->addFlash('success', 'Annonce créée avec succès.');
            return $this->redirectToRoute('app_annonces_index');
        }

        return $this->render('annonces/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_annonces_show', methods: ['GET'])]
    public function show(Annonces $annonce): Response
    {
        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annonces_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
      $user= $this->getUser();
        if (!$user){
         return $this->redirectToRoute('app_login');   
        }
        $userAnnonce= $annonce->getUser()->getId();
        $userId = $this->getUser()->getId();
        

        if ($userId != $userAnnonce) {
    return $this->redirectToRoute('app_annonces_index');
}


        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annonces/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonces_delete', methods: ['POST'])]
    public function delete(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
       $user= $this->getUser();
        if (!$user){
         return $this->redirectToRoute('app_login');   
        }
        $userAnnonce= $annonce->getUser()->getId();
        $userId = $this->getUser()->getId();
        

        if ($userId != $userAnnonce) {
    return $this->redirectToRoute('app_annonces_index');
}

        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonces_index', [], Response::HTTP_SEE_OTHER);
    }
}
