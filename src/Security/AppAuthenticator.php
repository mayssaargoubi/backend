<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; // Import nécessaire

class AppAuthenticator extends AbstractAuthenticator
{
    public function authenticate(Request $request): Passport
    {
        // Récupère l'email et le mot de passe
        $email = $request->request->get('_username');
        $password = $request->request->get('_password');

        if (null === $email || null === $password) {
            throw new AuthenticationException('Email or password cannot be empty');
        }

        // On utilise l'email comme identifiant pour l'authentification
        return new Passport(
            new UserBadge($email),  // Ici, l'email est l'identifiant de l'utilisateur
            new PasswordCredentials($password)  // Utilisation de PasswordCredentials
        );
    }

    public function supports(Request $request): ?bool
    {
        // On vérifie que la requête est une soumission de formulaire de connexion (POST)
        return $request->isMethod('POST') && $request->attributes->get('_route') === 'app_login';
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirige l'utilisateur vers la page d'accueil ou une autre page
        return new Response('Authentification réussie', 200);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // En cas d'échec, retourne une erreur
        return new Response('Erreur d\'authentification', 401);
    }
}
