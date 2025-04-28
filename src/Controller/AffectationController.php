<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AffectationController extends AbstractController
{
    #[Route('/api/affectation', name: 'api_affectation', methods: ['POST'])]
    public function affecter(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['manager_id']) || !isset($data['employee_id'])) {
            return new JsonResponse(['error' => 'Manager ou employé manquant.'], 400);
        }

        $manager = $em->getRepository(User::class)->find($data['manager_id']);
        $employee = $em->getRepository(User::class)->find($data['employee_id']);

        if (!$manager || !$employee) {
            return new JsonResponse(['error' => 'Manager ou employé introuvable.'], 404);
        }

        $employee->setMId($manager->getId());
        $em->flush();

        return new JsonResponse([
            'message' => 'Affectation réussie !',
            'employee_id' => $employee->getId(),
            'manager_id' => $manager->getId()
        ]);
    }
}
