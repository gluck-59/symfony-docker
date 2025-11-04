<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('user/list', name: 'user_list')]
    public function list(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect('/index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $password */
            $password = $form->get('password')->getData();


            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));

            // Roles from form (unmapped field 'roles')
            $formRoles = $form->get('roles')->getData();
            if (is_array($formRoles) && !empty($formRoles)) {
                $normalized = [];
                foreach ($formRoles as $role) {
                    if (!is_string($role) || $role === '') {
                        continue;
                    }
                    $role = strtoupper(trim($role));
                    if (str_starts_with($role, 'ROLE_')) {
                        $normalized[] = $role;
                    }
                }
                if (!empty($normalized)) {
                    $user->setRoles(array_values(array_unique($normalized)));
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('user_list');
        }

            $existingUsers = $userRepository->findAll();


        return $this->render('user/list.html.twig', [
            'title' => 'Юзеры',
            'existingUsers' => $existingUsers,
            'registrationForm' => $form,
        ]);
    }



    #[Route('/user/{id}/edit', name: 'user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = $form->get('password')->getData();
            // если есть новый пароль — установим
            if (!is_null($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);

//            $em->persist($user); // при обновлении не нужно?
            $em->flush();

            $this->addFlash('success', 'Пользователь успешно обновлен.');
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }




    #[Route('/user/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Пользователь удалён.');
        } else {
            $this->addFlash('error', 'Ошибка при удалении пользователя.');
        }

        return $this->redirectToRoute('user_list');
    }




}
