<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\Evaluation;
use App\Entity\User;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/feedback')]
final class FeedbackController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'api_feedback_index', methods: ['GET'])]
    public function index(FeedbackRepository $feedbackRepository): JsonResponse
    {
        $feedback = $feedbackRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize($feedback, 'json', ['groups' => 'feedback:read']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/', name: 'api_feedback_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $feedback = new Feedback();
        $feedback->setCommentaire($data['commentaire'] ?? '');
        $feedback->setFeedback($data['feedback'] ?? '');

        // Set relationships using entity references
        if (isset($data['evaluation_id'])) {
            $evaluation = $this->entityManager->getReference(Evaluation::class, $data['evaluation_id']);
            $feedback->setEvaluation($evaluation);
        }
        if (isset($data['manager_id'])) {
            $manager = $this->entityManager->getReference(User::class, $data['manager_id']);
            $feedback->setManager($manager);
        }
        if (isset($data['employee_id'])) {
            $employee = $this->entityManager->getReference(User::class, $data['employee_id']);
            $feedback->setEmployee($employee);
        }

        $errors = $this->validator->validate($feedback);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return new JsonResponse(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->persist($feedback);
        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($feedback, 'json', ['groups' => 'feedback:read']),
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_feedback_show', ['id' => $feedback->getId()])],
            true
        );
    }

    #[Route('/{id}', name: 'api_feedback_show', methods: ['GET'])]
    public function show(Feedback $feedback): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($feedback, 'json', ['groups' => 'feedback:read']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_feedback_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Feedback $feedback): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['commentaire'])) {
            $feedback->setCommentaire($data['commentaire']);
        }
        if (isset($data['feedback'])) {
            $feedback->setFeedback($data['feedback']);
        }

        $errors = $this->validator->validate($feedback);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['errors' => (string) $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($feedback, 'json', ['groups' => 'feedback:read']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_feedback_delete', methods: ['DELETE'])]
    public function delete(Feedback $feedback): JsonResponse
    {
        $this->entityManager->remove($feedback);
        $this->entityManager->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}