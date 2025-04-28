<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Form\Evaluation1Type;
use App\Repository\EvaluationRepository;
use App\Repository\ObjectifNoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/evaluation')]
final class EvaluationController extends AbstractController
{
    // GET: List all evaluations
    #[Route('/', name: 'api_evaluation_index', methods: ['GET'])]
    public function index(EvaluationRepository $evaluationRepository): JsonResponse
    {
        $evaluations = $evaluationRepository->findAll();
        $data = [];

        foreach ($evaluations as $evaluation) {
            $data[] = [
                'id' => $evaluation->getId(),
                'employee' => $evaluation->getEmployee()->getFirstname() . ' ' . $evaluation->getEmployee()->getLastname(),
                'dateCreation' => $evaluation->getDateCreation()->format('Y-m-d'),
                'dateEvaluation' => $evaluation->getDateEvaluation()->format('Y-m-d'),
                'noteGlobale' => $evaluation->getNoteGlobale(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    // POST: Create a new evaluation
    #[Route('/', name: 'api_evaluation_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ObjectifNoteRepository $objectifNoteRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Ensure required fields are present
        if (!isset($data['employeeId'], $data['dateCreation'], $data['dateEvaluation'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $evaluation = new Evaluation();
        $evaluation->setEmployee($data['employeeId']);
        $evaluation->setDateCreation(new \DateTime($data['dateCreation']));
        $evaluation->setDateEvaluation(new \DateTime($data['dateEvaluation']));

        // Calculate noteGlobale based on related ObjectifNotes
        $notes = $objectifNoteRepository->findByEvaluationPeriod(
            $evaluation->getEmployee(),
            $evaluation->getDateCreation(),
            $evaluation->getDateEvaluation()
        );

        $totalNote = 0;
        $totalPoids = 0;

        foreach ($notes as $note) {
            $objectif = $note->getObjectif();
            if ($objectif) {
                $poids = $objectif->getPoids();
                $totalNote += $note->getNote() * $poids;
                $totalPoids += $poids;
            }
        }

        $noteGlobale = $totalPoids > 0 ? $totalNote / $totalPoids : 0;
        $evaluation->setNoteGlobale($noteGlobale);

        $entityManager->persist($evaluation);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Evaluation created successfully',
            'evaluation' => [
                'id' => $evaluation->getId(),
                'noteGlobale' => $evaluation->getNoteGlobale(),
            ]
        ], Response::HTTP_CREATED);
    }

    // GET: Show specific evaluation
    #[Route('/{id}', name: 'api_evaluation_show', methods: ['GET'])]
    public function show(Evaluation $evaluation): JsonResponse
    {
        return new JsonResponse([
            'id' => $evaluation->getId(),
            'employee' => $evaluation->getEmployee()->getFirstname() . ' ' . $evaluation->getEmployee()->getLastname(),
            'dateCreation' => $evaluation->getDateCreation()->format('Y-m-d'),
            'dateEvaluation' => $evaluation->getDateEvaluation()->format('Y-m-d'),
            'noteGlobale' => $evaluation->getNoteGlobale(),
        ], Response::HTTP_OK);
        
    }

    // PUT: Update an evaluation
    #[Route('/{id}', name: 'api_evaluation_update', methods: ['PUT'])]
    public function update(
        Request $request,
        Evaluation $evaluation,
        EntityManagerInterface $entityManager,
        ObjectifNoteRepository $objectifNoteRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Ensure required fields are present
        if (!isset($data['dateCreation'], $data['dateEvaluation'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        // Update fields
        $evaluation->setDateCreation(new \DateTime($data['dateCreation']));
        $evaluation->setDateEvaluation(new \DateTime($data['dateEvaluation']));

        // Recalculate noteGlobale
        $notes = $objectifNoteRepository->findByEvaluationPeriod(
            $evaluation->getEmployee(),
            $evaluation->getDateCreation(),
            $evaluation->getDateEvaluation()
        );

        $totalNote = 0;
        $totalPoids = 0;

        foreach ($notes as $note) {
            $objectif = $note->getObjectif();
            if ($objectif) {
                $poids = $objectif->getPoids();
                $totalNote += $note->getNote() * $poids;
                $totalPoids += $poids;
            }
        }

        $noteGlobale = $totalPoids > 0 ? $totalNote / $totalPoids : 0;
        $evaluation->setNoteGlobale($noteGlobale);

        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Evaluation updated successfully',
            'evaluation' => [
                'id' => $evaluation->getId(),
                'noteGlobale' => $evaluation->getNoteGlobale(),
            ]
        ], Response::HTTP_OK);
    }

    // DELETE: Delete an evaluation
    #[Route('/{id}', name: 'api_evaluation_delete', methods: ['DELETE'])]
    public function delete(
        Evaluation $evaluation,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $entityManager->remove($evaluation);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Evaluation deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
