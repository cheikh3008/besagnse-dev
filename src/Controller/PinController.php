<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\Jaime;
use App\Form\PinType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\PinRepository;
use App\Repository\JaimeRepository;
use App\Repository\ProfilRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index( PinRepository $pinRepository, Request $request): Response
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
        ]);
    }

    
     
    /**
     * @Route("pin/new", name="pin_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_TAILLEUR')")
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
    public function show(Pin $pin, CommentaireRepository $commentaireRepository): Response
    {
        
        return $this->render('pin/show.html.twig', [
            'pin'  => $pin,
            'commentaire' => $commentaireRepository->findBy(array('pin' => $pin), array('updatedAt' => 'desc'), 3),
        ]);
    }

    /**
     * @Route("pin/{id}/edit", name="pin_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_TAILLEUR' and user === pin.getUser())")
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
     * @Security("is_granted('ROLE_TAILLEUR' and user === pin.getUser())")
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

    

  
}