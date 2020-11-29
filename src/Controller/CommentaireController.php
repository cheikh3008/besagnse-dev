<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{
    /**
     * @Route("pin/{id}/comment", name="pin_comment" )
     * cette fonction permet de commenter un pin
     * @param Pin $pin
     * @param CommentaireRepository $commentaireRepository
     * @return Response
     */
    public function add(Pin $pin, Request $request, CommentaireRepository $commentaireRepository): Response
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
        $commentaires = $commentaireRepository->findBy(['pin' => $pin, 'user' => $user], ['updatedAt' => 'asc']);
        $jsonData = [];
        foreach ($commentaires as $values) {
            $temp = array(
                'fullname' => $values->getUser()->getPrenom() . ' ' . $values->getUser()->getNom(),
                'message' => $values->getMessage()
            );
            $jsonData  = $temp;
        }
        return $this->json([
            'code' => 200,
            'form' => $form->createView(),
            'message' => 'like bien ajouté',
            'commentaire' => $jsonData,
            'nbCommentaire' => $commentaireRepository->count(['pin' => $pin, 'user' => $user])
        ]);
    }

    /**
     * @Route("commentaire/{id}", name="commentaire_delete", methods={"DELETE"})
     */
    public function delete( Request $request, CommentaireRepository $commentaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commentaireRepository->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commentaireRepository);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home');
    }
}
