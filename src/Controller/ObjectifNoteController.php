<?php

namespace App\Controller;

use App\Entity\ObjectifNote;
use App\Form\ObjectifNoteType;
use App\Repository\ObjectifNoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/objectifnote')]
class ObjectifNoteController extends AbstractController
{
    #[Route('/', name: 'app_objectif_note_index', methods: ['GET'])]
    public function index(ObjectifNoteRepository $objectifNoteRepository): Response
    {
        return $this->render('objectif_note/index.html.twig', [
            'objectif_notes' => $objectifNoteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_objectif_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $objectifNote = new ObjectifNote();
        $form = $this->createForm(ObjectifNoteType::class, $objectifNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($objectifNote);
            $entityManager->flush();

            return $this->redirectToRoute('app_objectif_note_index');
        }

        return $this->render('objectif_note/new.html.twig', [
            'objectif_note' => $objectifNote,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_objectif_note_show', methods: ['GET'])]
    public function show(ObjectifNote $objectifNote): Response
    {
        return $this->render('objectif_note/show.html.twig', [
            'objectif_note' => $objectifNote,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_objectif_note_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ObjectifNote $objectifNote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ObjectifNoteType::class, $objectifNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_objectif_note_index');
        }

        return $this->render('objectif_note/edit.html.twig', [
            'objectif_note' => $objectifNote,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_objectif_note_delete', methods: ['POST'])]
    public function delete(Request $request, ObjectifNote $objectifNote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$objectifNote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($objectifNote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_objectif_note_index');
    }
}
