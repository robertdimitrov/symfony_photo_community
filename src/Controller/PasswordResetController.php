<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordReset;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetController extends Controller
{
    /**
    * @Route("/forgot_password", name="forgot_password", methods={"GET", "POST"})
    */
    public function forgotPassword(Request $request, \Swift_Mailer $mailer)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse('/');
        }

        $form = $this->buildForgotPasswordForm();

        $form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid())
    	{
    		$user = $this->findUserByEmail($form->getData()['email']);

    		if ($user == null) {
    			$this->addFlash('error', 'No user with the email ' . $form->getData()['email'] . 'could be found.');
    			return $this->render('password_reset/forgot_password.html.twig', array(
		        	'form' => $form->createView()
		        ));
    		}
    		
    		$passwordReset = $this->createPasswordReset($user);
    		$this->sendEmail($user, $passwordReset, $mailer);

    		$this->addFlash('success', 'We\'ve sent you an email with a link for a password reset.');

    		return $this->redirectToRoute('home');
    	}

        return $this->render('password_reset/forgot_password.html.twig', array(
        	'form' => $form->createView()
        ));
    }

	/**
    * @Route("/reset_password", name="reset_password", methods={"GET", "POST"})
    */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
    	if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse('/');
        }

        $token = $request->query->get('token');
        $passwordReset = $this->findPasswordReset($token);

        if ($token == null || $passwordReset == null || $passwordReset->getIsUsed())
        {
        	return new RedirectResponse('/');
        }

        if ($this->passwordResetDateExpired($passwordReset))
        {
        	$this->addFlash('error', 'Date expired');
        }

        $form = $this->buildResetPasswordForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
			$password = $form->getData()['plainPassword'];
        	$this->updateUserPassword($passwordReset, $password, $passwordEncoder);

        	$this->updatePasswordReset($passwordReset);

        	$this->addFlash('success', 'We changed your password!');

    		return $this->redirectToRoute('home');
        }

        return $this->render('password_reset/reset_password.html.twig', array(
        	'form' => $form->createView()
        ));
    }

    private function buildForgotPasswordForm()
    {
    	return $this->createFormBuilder()
        	->add('email', EmailType::class, array(
        		'required' => true,
        		'constraints' => array(new Email())
        	))
        	->add('send', SubmitType::class)
        	->getForm();
    }

    private function buildResetPasswordForm()
    {
    	return $this->createFormBuilder()
    		->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'New Password'),
                'second_options' => array('label' => 'Repeat New Password'),
            ))
            ->add('send', SubmitType::class)
            ->getForm();
    }

    private function createPasswordReset($user)
    {
    	$passwordResetRepository = $this->getDoctrine()->getRepository(PasswordReset::class);
    	$passwordReset = $passwordResetRepository->findOneByUserId($user->getId());

    	if ($passwordReset == null)
    	{
    		$passwordReset = new PasswordReset();
    	}

    	$token = md5(random_bytes(16));
    	$expirationDate = new \DateTime();
    	$expirationDate->add(new \DateInterval('PT1H'));

    	$passwordReset->setUserId($user);
    	$passwordReset->setToken($token);
    	$passwordReset->setExpirationDate($expirationDate);
    	$passwordReset->setIsUsed(false);

    	$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($passwordReset);
		$entityManager->flush();

		return $passwordReset;
    }

    private function findPasswordReset($token)
    {
    	$passwordResetRepository = $this->getDoctrine()->getRepository(PasswordReset::class);
    	return $passwordResetRepository->findOneByToken($token);
    }

    private function passwordResetDateExpired($passwordReset)
    {
    	$now = new \DateTime();
    	$expirationDate = $passwordReset->getExpirationDate();

    	return $now > $expirationDate;
    }

    private function updateUserPassword($passwordReset, $password, $passwordEncoder)
    {
    	$user = $passwordReset->getUserId();

		$password = $passwordEncoder->encodePassword($user, $password);

		$user->setPassword($password);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user);
		$entityManager->flush();
    }

    private function updatePasswordReset($passwordReset)
    {
    	$passwordReset->setIsUsed(true);

    	$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($passwordReset);
		$entityManager->flush();
    }

    private function findUserByEmail($email)
    {
    	$userRepository = $this->getDoctrine()->getRepository(User::class);
    	return $userRepository->findOneByEmail($email);
    }

    private function sendEmail($user, $passwordReset, $mailer)
    {
    	$message = (new \Swift_Message('Reset your password'))
        ->setFrom('test@example.com')
        ->setTo($user->getEmail())
        ->setBody(
            $this->renderView(
                'emails/forgot_password.html.twig',
                array('token' => $passwordReset->getToken())
            ),
            'text/html'
    	);

    	$mailer->send($message);
    }

}