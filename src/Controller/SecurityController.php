<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends Controller
{
    /**
    * @Route("/login", name="login")
    */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse('/');
        }

    	$error = $authenticationUtils->getLastAuthenticationError();

    	$lastUsername = $authenticationUtils->getLastUsername();

    	return $this->render('security/login.html.twig', array(
    		'error' => $error,
    		'last_username' => $lastUsername
    	));
    }

    /**
    * @Route("/logout", name="logout")
    */
    public function logout()
    {
        return $this->redirectToRoute('home');
    }
}
