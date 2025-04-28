<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/notifications')]
final class NotificationController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'api_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): JsonResponse
    {
        $notifications = $notificationRepository->findAll();
        return $this->json(
            $notifications,
            Response::HTTP_OK,
            [],
            ['groups' => 'notification:read']
        );
    }

    #[Route('/', name: 'api_notification_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $notification = new Notification();
        $notification->setMessage($data['message'] ?? '');
        $notification->setSeen($data['seen'] ?? false);
        
        if (isset($data['destinataire_id'])) {
            $user = $this->entityManager->getReference(User::class, $data['destinataire_id']);
            $notification->setDestinataire($user);
        }

        $errors = $this->validator->validate($notification);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => (string) $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $this->json(
            $notification,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_notification_show', ['id' => $notification->getId()])],
            ['groups' => 'notification:read']
        );
    }

    #[Route('/{id}', name: 'api_notification_show', methods: ['GET'])]
    public function show(Notification $notification): JsonResponse
    {
        return $this->json(
            $notification,
            Response::HTTP_OK,
            [],
            ['groups' => 'notification:read']
        );
    }

    #[Route('/{id}', name: 'api_notification_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Notification $notification): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['message'])) {
            $notification->setMessage($data['message']);
        }
        
        if (isset($data['seen'])) {
            $notification->setSeen($data['seen']);
        }

        $errors = $this->validator->validate($notification);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => (string) $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->flush();

        return $this->json(
            $notification,
            Response::HTTP_OK,
            [],
            ['groups' => 'notification:read']
        );
    }

    #[Route('/{id}', name: 'api_notification_delete', methods: ['DELETE'])]
    public function delete(Notification $notification): JsonResponse
    {
        $this->entityManager->remove($notification);
        $this->entityManager->flush();
        
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}