<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Photo;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin", methods={"GET"})
     */
    public function index(Request $request)
    {
    	$page = (int) $request->query->get('page') ?: 1;

    	$photoRepository = $this->getDoctrine()->getRepository(Photo::class);
    	$pendingPhotos = $photoRepository->findByStatus('pending', $page);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'photos' => $pendingPhotos
        ]);
    }

    /**
    * @Route("/photos/{photoId}/admin", name="admin_photo", methods={"POST"})
    */
    public function adminPhotoAction($photoId, Request $request)
    {
		$photoRepository = $this->getDoctrine()->getRepository(Photo::class);
		$photo = $photoRepository->findOneById($photoId);


		if ($photo == null) {
			return new JsonResponse(array('status' => 'failed', 'message' => 'Photo with ID ' . $photoId . ' not found'));
		}

		$newStatus = $request->query->get('status');

		if ($newStatus == null) {
		 	return new JsonResponse(array('status' => 'failed', 'message' => 'No status parameter sent')); 
		}

		try {
			$photo->setStatus($newStatus);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($photo);
            $entityManager->flush();
            $status = 'success';
        } catch (\Exception $e) {
            $status = $e->getMessage();
        }

        return new JsonResponse(array('status' => $status));
    }
}
