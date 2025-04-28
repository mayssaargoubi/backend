<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer
    ) {}

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $users = $userRepository->findAll();
        
        if ($request->headers->get('Accept') === 'application/json') {
            return new JsonResponse(
                $this->serializer->serialize($users, 'json', ['groups' => 'user:read']),
                Response::HTTP_OK,
                [],
                true
            );
        }

        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        
        if ($request->headers->get('Accept') === 'application/json') {
            $data = json_decode($request->getContent(), true);
            $user->setEmail($data['email'] ?? '');
            $user->setFirstname($data['firstname'] ?? '');
            $user->setLastname($data['lastname'] ?? '');
            // Set other fields as needed
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            return new JsonResponse(
                ['id' => $user->getId(), 'message' => 'User created successfully'],
                Response::HTTP_CREATED
            );
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, Request $request): Response
    {
        if ($request->headers->get('Accept') === 'application/json') {
            return new JsonResponse(
                $this->serializer->serialize($user, 'json', ['groups' => 'user:read']),
                Response::HTTP_OK,
                [],
                true
            );
        }

        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($request->headers->get('Accept') === 'application/json') {
            $data = json_decode($request->getContent(), true);
            
            if (isset($data['email'])) $user->setEmail($data['email']);
            if (isset($data['firstname'])) $user->setFirstname($data['firstname']);
            // Update other fields as needed
            
            $entityManager->flush();
            
            return new JsonResponse(
                ['message' => 'User updated successfully'],
                Response::HTTP_OK
            );
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($request->headers->get('Accept') === 'application/json') {
            $entityManager->remove($user);
            $entityManager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }
}