<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    // Route for traditional HTML form login
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Updated: Route for API JSON login (to get JWT token)
    // This endpoint will be handled automatically by the JSON login authenticator
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(): JsonResponse
    {
        // This route is just a placeholder - the actual work is done by json_login authenticator
        // LexikJWTAuthenticationBundle will handle the token generation
        
        // This should never be reached in normal operation
        return new JsonResponse(['message' => 'Login endpoint. POST your credentials (email & password) here.'], Response::HTTP_OK);
    }

    // Route for logout
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony handles logout automatically
    }

    // Optional: Add a route to check if user is authenticated (for API)
    #[Route('/api/check-auth', name: 'api_check_auth')]
    public function checkAuth(): JsonResponse
    {
        $user = $this->getUser();
        
        if (null === $user) {
            return new JsonResponse(['authenticated' => false], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'authenticated' => true,
            'user' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }
}