<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends AbstractController
{
    /**
    * @Route("/register", name="user_registration")
    */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse('/');
        }
        
    	$user = new User();

    	$form = $this->createForm(UserType::class, $user);

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid())
    	{
    		$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
    		$user->setPassword($password);

    		$entityManager = $this->getDoctrine()->getManager();
    		$entityManager->persist($user);
    		$entityManager->flush();

    		$this->addFlash('success', 'Welcome ' . $user->getEmail());

    		$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
	        $this->container->get('security.token_storage')->setToken($token);
	        $this->container->get('session')->set('_security_main', serialize($token));

    		return $this->redirectToRoute('home');
    	}

    	return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }

}