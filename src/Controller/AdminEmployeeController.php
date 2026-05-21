<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/employees')]
#[IsGranted('ROLE_ADMIN')]
class AdminEmployeeController extends AbstractController
{
    #[Route('', name: 'app_admin_employees_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        $employees = $em->getRepository(Utilisateur::class)->findBy(['role' => 'EMPLOYE']);

        return $this->render('admin/employees/list.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/new', name: 'app_admin_employees_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if ($request->isMethod('POST')) {
            $prenom = $request->request->get('prenom');
            $nom = $request->request->get('nom');
            $mail = $request->request->get('mail');
            $password = $request->request->get('password');

            // Vérifier que l'email n'existe pas
            $existingUser = $em->getRepository(Utilisateur::class)->findOneBy(['mail' => $mail]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email existe déjà');
                return $this->redirectToRoute('app_admin_employees_new');
            }

            // Créer l'employé
            $employee = new Utilisateur();
            $employee->setPrenom($prenom);
            $employee->setNom($nom);
            $employee->setMail($mail);
            $employee->setRole('EMPLOYE');
            $employee->setActif(true);

            $hashedPassword = $passwordHasher->hashPassword($employee, $password);
            $employee->setMdpHash($hashedPassword);

            $em->persist($employee);
            $em->flush();

            $this->addFlash('success', 'Employé créé avec succès');
            return $this->redirectToRoute('app_admin_employees_list');
        }

        return $this->render('admin/employees/new.html.twig');
    }

    #[Route('/{id}/toggle', name: 'app_admin_employees_toggle', methods: ['POST'])]
    public function toggle(
        Utilisateur $employee,
        EntityManagerInterface $em
    ): Response
    {
        // Vérifier que c'est bien un employé
        if ($employee->getRole() !== 'EMPLOYE') {
            $this->addFlash('error', 'Seuls les employés peuvent être activés/désactivés');
            return $this->redirectToRoute('app_admin_employees_list');
        }

        // Basculer l'état
        $employee->setActif(!$employee->isActif());
        $em->flush();

        $state = $employee->isActif() ? 'activé' : 'désactivé';
        $this->addFlash('success', "Employé $state");

        return $this->redirectToRoute('app_admin_employees_list');
    }

    #[Route('/{id}/delete', name: 'app_admin_employees_delete', methods: ['POST'])]
    public function delete(
        Utilisateur $employee,
        EntityManagerInterface $em
    ): Response
    {
        if ($employee->getRole() !== 'EMPLOYE') {
            $this->addFlash('error', 'Seuls les employés peuvent être supprimés');
            return $this->redirectToRoute('app_admin_employees_list');
        }

        $em->remove($employee);
        $em->flush();

        $this->addFlash('success', 'Employé supprimé');

        return $this->redirectToRoute('app_admin_employees_list');
    }
}
