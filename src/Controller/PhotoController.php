<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Comment;
use App\Entity\PhotoLike;
use App\Form\PhotoType;
use App\Form\CommentType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

class PhotoController extends Controller
{
    /**
     * @Route("/photos", name="photos", methods={"GET"})
     */
    public function index()
    {
        $photoRepository = $this->getDoctrine()->getRepository(Photo::class);
        $approvedPhotos = $photoRepository->approvedPhotos();

        $user = $this->getUser();

        foreach ($approvedPhotos as $photo)
        {
            $photo->isLiked = false;
            $photo->likeId = null;

            $likes = $photo->getPhotoLikes()->toArray();

            foreach($likes as $like)
            {
                if ($like->getUserId() == $user)
                {
                    $photo->isLiked = true;
                    $photo->likeId = $like->getId();
                    break;
                }
            }
        }

        return $this->render('photo/index.html.twig', [
            'photos' => $approvedPhotos
        ]);
    }

    /**
    * @Route("/photos/{photo}", name="show_photo", methods={"GET"})
    */
    public function show($photo)
    {
        $photoRepository = $this->getDoctrine()->getRepository(Photo::class);
        $photo = $photoRepository->findOneById($photo);

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

    	return $this->render('photo/show.html.twig', [
    		'photo' => $photo,
            'form' => $form->createView()
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

    /**
    * @Route("/photos/{photo}/comments", name="submit_comment", methods={"POST"})
    */
    public function submitComment($photo, Request $request, LoggerInterface $logger)
    {
        $comment = new Comment();

        $logger->info('Loggin request');
        $logger->info($request);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $photoRepository = $this->getDoctrine()->getRepository(Photo::class);
            $photo = $photoRepository->findOneById($photo);

            $comment->setUserId($this->getUser());
            $comment->setPhotoId($photo);
            
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();
                $status = 'success';
            } catch (\Exception $e) {
                $status = $e->getMessage();
            }
        } else {
            $status = 'failed';
        }

        return new JsonResponse(array('status' => $status));
    }

    /**
    * @Route("/photos/{photo}/likes", name="like_photo", methods={"POST"})
    */
    public function likePhoto($photo, Request $request)
    {
        $photoRepository = $this->getDoctrine()->getRepository(Photo::class);
        $photo = $photoRepository->findOneById($photo);

        $likeRepository = $this->getDoctrine()->getRepository(PhotoLike::class);
        $like = $likeRepository->findByPhotoAndUser($photo, $this->getUser());

        if ($like)
        {
           return new JsonResponse(array('status' => 'failed', 'message' => 'already liked')); 
        }

        $like = new PhotoLike();
        $like->setPhotoId($photo);
        $like->setUserId($this->getUser());

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($like);
            $entityManager->flush();
            return new JsonResponse(array('status' => 'success', 'likeId' => $like->getId()));
        } catch (\Exception $e) {
            return new JsonResponse(array('status' => $e->getMessage()));
        }
    }

    /**
    * @Route("/photos/{photo}/likes/{like}", name="unlike_photo", methods={"DELETE"})
    */
    public function unlikePhoto($photo, $like, Request $request)
    {
        $likeRepository = $this->getDoctrine()->getRepository(PhotoLike::class);
        $photoLike = $likeRepository->findById($like);

        // return new JsonResponse(array('status' => 'abc'));

        if ($photoLike == null)
        {
            return new JsonResponse(array('status' => 'failed', 'message' => 'Like object with id ' . $like . ' doesn\'t exist.')); 
        }

        if ($photoLike->getUserId() != $this->getUser())
        {
            return new JsonResponse(array('status' => 'failed', 'message' => 'You don\'t have the permissions to perform this action.'));
        }

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($photoLike);
            $entityManager->flush();
            $status = 'success';
        } catch (\Exception $e) {
            $status = $e->getMessage();
        }

        return new JsonResponse(array('status' => $status));
    }
}
