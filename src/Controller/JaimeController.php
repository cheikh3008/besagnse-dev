<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\Jaime;
use App\Repository\JaimeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JaimeController extends AbstractController
{
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
        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => 'Permission non accordéé',
            ], 403);
        }
        if ($pin->isLikedByUser($user)) {
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

    
}
