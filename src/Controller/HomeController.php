<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route({"/", "/home"}, name="home", methods={"GET"})
     */
    public function index()
    {
        $user = $this->getUser();
        $roles = '';

        if ($user != null)
        {
            $roles = implode($user->getRoles());
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'roles' => $roles
        ]);
    }
}
