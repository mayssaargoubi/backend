<?php
// src/Controller/LoginController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginController extends AbstractController
{
    // Traditional HTML form login (for web interface)
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // API JSON login endpoint (returns JWT token)
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(Request $request, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        // This is just a fallback - the actual authentication is handled by json_login in security.yaml
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return new JsonResponse([
                'message' => 'Authentication failed',
                'error' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'token' => $jwtManager->create($user),
            'user' => [
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }

    // Check authentication status
    #[Route('/api/check-auth', name: 'api_check_auth', methods: ['GET'])]
    public function checkAuth(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof UserInterface) {
            return new JsonResponse([
                'authenticated' => false
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'authenticated' => true,
            'user' => [
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }

    // Dashboard route (protected)
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('dashboard/dashboard.html.twig');
    }

    // Logout endpoint for API (JWT is stateless, but you might want to implement token invalidation)
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function apiLogout(): JsonResponse
    {
        // In a real implementation, you would invalidate the JWT token here
        return new JsonResponse([
            'message' => 'Successfully logged out'
        ]);
    }
}