<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestNavbarController extends AbstractController
{
    #[Route('/test-navbar')]
    public function test(): Response
    {
        $user = $this->getUser();
        if (!$user) return new Response('Pas connecté');
        
        return new Response(
            'User: ' . $user->getMail() . '<br>' .
            'Roles: ' . json_encode($user->getRoles()) . '<br>' .
            'Has ROLE_ADMIN: ' . (in_array('ROLE_ADMIN', $user->getRoles()) ? 'OUI' : 'NON')
        );
    }
}
