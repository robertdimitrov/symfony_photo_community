<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhotoController extends Controller
{
    /**
     * @Route("/photos", name="photos", methods={"GET"})
     */
    public function index()
    {
        return $this->render('photo/index.html.twig', [
            'controller_name' => 'PhotoController',
        ]);
    }

    /**
    * @Route("/photos/{photo}", name="show_photo", methods={"GET"})
    */
    public function show($photo)
    {
    	return $this->render('photo/show.html.twig', [
    		'photo' => $photo
    	]);
    }
}
