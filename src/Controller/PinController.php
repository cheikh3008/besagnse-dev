<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\Jaime;
use App\Form\PinType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\PinRepository;
use App\Repository\JaimeRepository;
use App\Repository\CommentaireRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PinController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        
    }
    
    /**
     * @Route("/", name="app_home", methods={"GET" , "POST"})
     */
    public function index( PinRepository $pinRepository, Request $request, ProfilRepository $profilRepository): Response
    {
        
        // $data =  $commentaireRepository->findBy(array('pin' => $pin), array('updatedAt' => 'desc'));
        // if ($request->isXmlHttpRequest()) {
        //     $jsonData = array();
        //     $idx = 0;
        //     foreach ($data as $values) {
        //         $temp = array(
        //             'fullname' => $values->getUser()->getPrenom().' '. $values->getUser()->getNom(),
        //             'message' => $values->getMessage(),
        //             'pin' => $values->getPin()->getId()
        //         );
        //         $jsonData[$idx++] = $temp;
        //     }
        //     return new JsonResponse($jsonData);
        // } 

        return $this->render('pin/index.html.twig', [
            'pins' => $pinRepository->findBy(array(), array('updatedAt' => 'DESC')),
            'profil' => $profilRepository->findOneBy(['user' => $this->getUser()], ['updatedAt' => 'desc'])
        ]);
    }
    
    /**
     * @Route("pin/new", name="pin_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $userConnect = $this->tokenStorage->getToken()->getUser();
        $pin = new Pin();
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $pin->setUser($userConnect);
            $entityManager->persist($pin);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pin/new.html.twig', [
            'pin' => $pin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("pin/{id}", name="pin_show", methods={"GET","POST"})
     */
    public function show(Pin $pin, Request $request, CommentaireRepository $commentaireRepository, ProfilRepository $profilRepository): Response
    {
        
        return $this->render('pin/show.html.twig', [
            'pin'  => $pin,
            'commentaire' => $commentaireRepository->findBy(array('pin' => $pin), array('updatedAt' => 'desc'), 3),
            'profil' => $profilRepository->findOneBy(['user' => $this->getUser()], ['updatedAt' => 'desc'])
        ]);
    }

    /**
     * @Route("pin/{id}/edit", name="pin_edit", methods={"GET","POST"})
     * 
     */
    public function edit(Request $request, Pin $pin): Response
    {
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pin/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pin_delete", methods={"DELETE"})
     * @IsGranted("ROLE_TAILLEUR")
     */
    public function delete(Request $request, Pin $pin): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pin->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("pin/{id}/like", name="pin_like")
     * cette fonction permet de liker un pin
     * @param Pin $pin
     * @param JaimeRepository $jaimeRepository
     * @return Response
     */
    public function like(Pin $pin, JaimeRepository $jaimeRepository): Response
    {
        
        $user = $this->getUser();
        if(!$user){
            return $this->json([
                'code' => 403,
                'message' => 'Permission non accordéé',
            ], 403);
        }
        if($pin->isLikedByUser($user) ){
            $like = $jaimeRepository->findOneBy([
                'pin' => $pin,
                'user' => $user
            ]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($like);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'like bien supprimé',
                'likes' => $jaimeRepository->count([
                    'pin' => $pin 
                ])
            ], 200);
        }
        $like  = new Jaime();
        $entityManager = $this->getDoctrine()->getManager();
        $like->setUser($user); 
        $like->setPin($pin);
        $entityManager->persist($like);

        $entityManager->flush();
        return $this->json([
            'code' => 200,
            'message' => 'like bien ajouté',
            'likes' => $jaimeRepository->count([
                'pin' => $pin
            ])
        ], 200);
    }

    /**
     * @Route("pin/{id}/comment", name="pin_comment" )
     * cette fonction permet de commenter un pin
     * @param Pin $pin
     * @param CommentaireRepository $commentaireRepository
     * @return Response
     */
    public function comment(Pin $pin , Request $request , CommentaireRepository $commentaireRepository, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => 'Permission non accordéé',
            ], 403);
        }
        
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
    
        $entityManager = $this->getDoctrine()->getManager();
        $commentaire->setUser($user);
        $commentaire->setPin($pin);
        $commentaire->setMessage($request->request->get('message'));
        $entityManager->persist($commentaire);
        $entityManager->flush();
        $commentaires = $commentaireRepository->findByUser($pin->getId());
        
        // if ($request->isXmlHttpRequest()) {
        //     return new JsonResponse([
        //         $this->renderView('pin/_commentaire.html.twig', [
        //             'commentaires' => $commentaires
        //         ])
        //     ]);
        // }
        return $this->json([
            'code' => 200,
            'message' => 'like bien ajouté'
        ]);

    }

  
}