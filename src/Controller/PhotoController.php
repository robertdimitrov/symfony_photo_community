<?php

namespace App\Controller;

use App\Entity\Photo;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\PhotoType;

class PhotoController extends Controller
{
    /**
     * @Route("/photos", name="photos", methods={"GET"})
     */
    public function index()
    {
        $photoRepository = $this->getDoctrine()->getRepository(Photo::class);
        $approvedPhotos = $photoRepository->approvedPhotos();

        return $this->render('photo/index.html.twig', [
            'photos' => $approvedPhotos
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

    /**
    * @Route("/upload", name="upload_photo", methods={"GET", "POST"})
    */
    public function upload(Request $request)
    {
        $photo = new Photo();
        
        $form = $this->createForm(PhotoType::class, $photo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $photo->getImageFile();

            $filename = uniqid('img_', true).'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('photos_directory'),
                $filename
            );

            $photo->setCreatedAt(new \DateTime());
            $photo->setStatus('pending');
            $photo->setUserId($this->getUser());
            $photo->setFilename($filename);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($photo);
            $entityManager->flush();

            $this->addFlash('success', 'Thank you for uploading this picture! We will inform you when it has been approved by our moderators.');

            return $this->redirectToRoute('home');
        }

        return $this->render('photo/upload.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
