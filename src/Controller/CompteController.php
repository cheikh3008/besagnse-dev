<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Form\ProfilType;
use App\Form\EditProfileType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompteController extends AbstractController
{
    /**
     * @Route("/password-update", name="app_password_update")
     * cette fonction permet de mettre à jour son mot passe
     */
    public function password_upadte(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $password_upadte = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $password_upadte);
        $form->handleRequest($request);
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            // if (!password_verify($password_upadte->getOldPassword(), $user->getPassword())) {
            //     $form->get('oldPassword')->addError(
            //         new FormError("Le mot passe que vous avez tapé est incorrect ")
            //     );
            // } else {
                $newPassword = $password_upadte->getNewPassword();
                $hash = $passwordEncoder->encodePassword($user, $newPassword);
                $user->setPassword($hash);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_compte_user', ['id' => $user->getId()]);
            //}
        }

        return $this->render('compte/password-update.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("profil-user/{id}", name="app_compte_user")
     * Cette fonction permet de voir le compte d'un utilisateur
     */
    public function userCompte(PinRepository $pinRepository, int $id, Request $request, ProfilRepository $profilRepository, UserRepository $userRepository)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $profil = new Profil();
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /**
             * elle permet de supprimer un profil d'utilisateur s'il en existe dans la base de donnéés
             */
            if ($user->isUserProfil($user) === true) {
                $pp = $profilRepository->findBy(['user' => $user]);
                foreach ($pp as $value) {
                    $entityManager->remove($value);
                }
            }
            $profil->setUser($user);
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_compte_user', ['id' => $id]);
        }

        return $this->render('compte/compte-user.html.twig', [
            'user' =>   $user = $userRepository->findOneById($id),
            //retourne l'ensemble des pins d'un user
            'pin' => $pinRepository->findBy(array('user' => $user), array('updatedAt' => 'desc')),
            'form' => $form->createView(),
            //retourne le profil d'un user
            'profil' => $profilRepository->findOneBy(['user' => $user], ['updatedAt' => 'desc'])
        ]);
    }

    /**
     * @Route("/edit-profil", name="app_edit_profil")
     * Cette fonction permet de modifier les informations d'un utilisateur
     */
    public function profil(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_compte_user', ['id' => $user->getId()]);
        }
        return $this->render('compte/edit-compte.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
