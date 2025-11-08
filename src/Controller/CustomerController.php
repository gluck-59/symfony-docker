<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
//use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/customer')]
final class CustomerController extends AbstractController
{
    #[Route('', name: 'customer_index', methods: ['GET'])]
    public function index(Request $request, CustomerRepository $customerRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $isAdmin = $this->isGranted('ROLE_ADMIN');

//prettyDump($isAdmin);

        if ($isAdmin) {
            $customers = $customerRepository->findBy([], ['id' => 'DESC']);
        } else {
            $customers = $customerRepository->findBy(['creator' => $user->getId()], ['id' => 'DESC']);
        }

        // Подготовим карту creatorId => username для отображения в интерфейсе
        $creatorIds = array_unique(array_map(static fn(Customer $c) => $c->getCreator(), $customers));
        $creators = [];
        if (!empty($creatorIds)) {
            $users = $userRepository->createQueryBuilder('u')
                ->where('u.id IN (:ids)')
                ->setParameter('ids', $creatorIds)
                ->getQuery()->getResult();
            foreach ($users as $u) {
                $creators[$u->getId()] = $u->getUsername();
            }
        }

        return $this->render('customer/index.html.twig', [
            'title' => 'Клиенты',
            'customers' => $customers,
            'is_admin' => $isAdmin,
            'creators' => $creators,
        ]);
    }

    #[Route('/add', name: 'customer_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $customer = new Customer();

        if ($request->isMethod('POST')) {
            $name = trim((string) $request->request->get('name'));
            $data = $request->request->get('data');
            $parentId = $request->request->get('parentId');

            if ($name === '') {
                $this->addFlash('error', 'Название обязательно');
            } else {
                $customer->setName($name);
                $customer->setData($data ?: null);
                $customer->setParentId($parentId !== null && $parentId !== '' ? (int) $parentId : 0);
                $customer->setCreator($user->getId());

                $em->persist($customer);
                $em->flush();

                $this->addFlash('success', 'Клиент создан');
                return $this->redirectToRoute('customer_index');
            }
        }
        $classMetadata = $em->getClassMetadata(Customer::class);

        return $this->render('customer/new.html.twig', [
            'title' => 'Новый клиент',
            'customer' => $customer,
            'maxLengthData' => $classMetadata->getFieldMapping('data')['length'] ?? 255,
            'maxLengthName' => $classMetadata->getFieldMapping('name')['length'] ?? 64,
        ]);
    }

    #[Route('/{id}', name: 'customer_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(Customer $customer, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isGranted('ROLE_ADMIN') && $customer->getCreator() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $creatorUsername = null;
        if ($this->isGranted('ROLE_ADMIN')) {
            $creatorUser = $userRepository->find($customer->getCreator());
            $creatorUsername = $creatorUser?->getUsername();
        }

        return $this->render('customer/card.html.twig', [
            'title' => 'Карточка клиента',
            'customer' => $customer,
            'creator_username' => $creatorUsername,
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[Route('/{id}/edit', name: 'customer_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isGranted('ROLE_ADMIN') && $customer->getCreator() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $name = trim((string) $request->request->get('name'));
            $data = $request->request->get('data');
            $parentId = $request->request->get('parentId');

            if ($name === '') {
                $this->addFlash('error', 'Название обязательно');
            } else {
                $customer->setName($name);
                $customer->setData($data ?: null);
                $customer->setParentId($parentId !== null && $parentId !== '' ? (int) $parentId : 0);

                $em->flush();
                $this->addFlash('success', 'Клиент обновлён');
                return $this->redirectToRoute('customer_index');
            }
        }
        $classMetadata = $em->getClassMetadata(Customer::class);

        return $this->render('customer/edit.html.twig', [
            'title' => 'Редактирование клиента',
            'customer' => $customer,
            'maxLengthData' => $classMetadata->getFieldMapping('data')['length'] ?? 255,
            'maxLengthName' => $classMetadata->getFieldMapping('name')['length'] ?? 64,
        ]);
    }

    #[Route('/{id}', name: 'customer_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function delete(Request $request, Customer $customer, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isGranted('ROLE_ADMIN') && $customer->getCreator() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        $submittedToken = (string) $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete_customer_' . $customer->getId(), $submittedToken)) {
            $em->remove($customer);
            $em->flush();
            $this->addFlash('success', 'Клиент удалён');
        } else {
            $this->addFlash('error', 'Неверный CSRF токен');
        }

        return $this->redirectToRoute('customer_index');
    }
}
