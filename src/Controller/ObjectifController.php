<?php

namespace App\Controller;
use App\Entity\User;

use App\Entity\Objectif;
use App\Repository\ObjectifRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/objectif')]
final class ObjectifController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('/', name: 'api_objectif_index', methods: ['GET'])]
    public function index(ObjectifRepository $objectifRepository): JsonResponse
    {
        $objectifs = $objectifRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize($objectifs, 'json', ['groups' => 'objectif:read']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/', name: 'api_objectif_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $objectif = new Objectif();
        $objectif->setTitre($data['titre'] ?? '');
        $objectif->setDescription($data['description'] ?? '');
        $objectif->setDateCreation(new \DateTime());
        $objectif->setDateEcheance(new \DateTime($data['dateEcheance'] ?? 'now'));
        $objectif->setPoid($data['poid'] ?? 0);
        
        // Set manager/employee if IDs provided
        if (isset($data['manager_id'])) {
            $manager = $entityManager->getReference(User::class, $data['manager_id']);
            $objectif->setManager($manager);
        }
        if (isset($data['employee_id'])) {
            $employee = $entityManager->getReference(User::class, $data['employee_id']);
            $objectif->setEmployee($employee);
        }

        // Validate
        $errors = $this->validator->validate($objectif);
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

        $entityManager->persist($objectif);
        $entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($objectif, 'json', ['groups' => 'objectif:read']),
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_objectif_show', ['id' => $objectif->getId()])],
            true
        );
    }

    #[Route('/{id}', name: 'api_objectif_show', methods: ['GET'])]
    public function show(Objectif $objectif): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($objectif, 'json', ['groups' => 'objectif:read']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_objectif_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Objectif $objectif, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['titre'])) $objectif->setTitre($data['titre']);
        if (isset($data['description'])) $objectif->setDescription($data['description']);
        if (isset($data['dateEcheance'])) $objectif->setDateEcheance(new \DateTime($data['dateEcheance']));
        if (isset($data['poid'])) $objectif->setPoid($data['poid']);
        
        // Update relationships if provided
        if (isset($data['manager_id'])) {
            $manager = $entityManager->getReference(User::class, $data['manager_id']);
            $objectif->setManager($manager);
        }
        if (isset($data['employee_id'])) {
            $employee = $entityManager->getReference(User::class, $data['employee_id']);
            $objectif->setEmployee($employee);
        }

        $errors = $this->validator->validate($objectif);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['errors' => (string) $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        $entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($objectif, 'json', ['groups' => 'objectif:read']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'api_objectif_delete', methods: ['DELETE'])]
    public function delete(Objectif $objectif, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($objectif);
        $entityManager->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}