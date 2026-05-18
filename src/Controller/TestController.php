<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test-roles')]
    public function test(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new Response('Pas connecté');
        }

        $roles = $user->getRoles();
        return new Response(
            'User: ' . $user->getMail() . '<br>' .
            'Role (BD): ' . $user->getRole() . '<br>' .
            'Roles (array): ' . json_encode($roles)
        );
    }
}
