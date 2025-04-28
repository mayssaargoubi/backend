<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private LoggerInterface $logger
    ) {}

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            
            // Validate required fields
            $requiredFields = ['email', 'password', 'role'];
            if ($missingFields = array_diff($requiredFields, array_keys($data))) {
                return $this->jsonError('Missing required fields: ' . implode(', ', $missingFields), Response::HTTP_BAD_REQUEST);
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->jsonError('Invalid email format', Response::HTTP_BAD_REQUEST);
            }

            // Validate password complexity
            if (!$this->isPasswordValid($data['password'])) {
                return $this->jsonError(
                    'Password must be at least 8 characters and contain at least one number and one special character',
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Validate and normalize role
            $role = $this->normalizeRole($data['role']);
            if (!$this->isValidRole($role)) {
                return $this->jsonError('Invalid user role specified', Response::HTTP_BAD_REQUEST);
            }

            // Check existing user
            if ($entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
                return $this->jsonError('Email already registered', Response::HTTP_CONFLICT);
            }

            // Create and populate user
            $user = new User();
            $user->setEmail($data['email'])
                ->addRole($role);

            // Optional fields
            $optionalFields = ['firstname', 'lastname', 'm_id', 'statut'];
            foreach ($optionalFields as $field) {
                if (isset($data[$field])) {
                    $setter = 'set' . ucfirst($field);
                    $user->$setter($data[$field]);
                }
            }

            // Validate entity
            $errors = $this->validator->validate($user, null, ['registration']);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            // Hash password and save
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json([
                'message' => 'Registration successful',
                'user' => $this->serializeUser($user)
            ], Response::HTTP_CREATED);

        } catch (\JsonException $e) {
            return $this->jsonError('Invalid JSON payload', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error('Registration error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->jsonError('An error occurred during registration', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function jsonError(string $message, int $status): JsonResponse
    {
        return $this->json(['message' => $message], $status);
    }

    private function isPasswordValid(string $password): bool
    {
        return strlen($password) >= 8 
            && preg_match('/[0-9]/', $password) 
            && preg_match('/[^A-Za-z0-9]/', $password);
    }

    private function normalizeRole(string $role): string
    {
        $role = strtoupper($role);
        return str_starts_with($role, 'ROLE_') ? $role : 'ROLE_' . $role;
    }

    private function isValidRole(string $role): bool
    {
        $allowedRoles = ['ROLE_EMPLOYEE', 'ROLE_MANAGER', 'ROLE_HR'];
        return in_array($role, $allowedRoles, true);
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'm_id' => $user->getMId(),
            'statut' => $user->getStatut()
        ];
    }
}