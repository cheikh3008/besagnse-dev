<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\User;
use App\Entity\Profil;
use App\Form\ProfilType;
use App\Form\EditProfileType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Repository\PinRepository;
use App\Form\RegistrationFormType;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use App\Security\FormLoginAuthenticator;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, FormLoginAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/edit-profil", name="app_edit_profil")
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
            return $this->redirectToRoute('app_compte_user');
        }
        return $this->render('registration/edit-profil.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/password-update", name="app_password_update")
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
            if(!password_verify($password_upadte->getOldPassword(), $user->getPassword())){
                $form->get('oldPassword')->addError(
                    new FormError("Le mot passe que vous avez tapé est incorrect ")
                );
            }else{
                $newPassword = $password_upadte->getNewPassword();
                $hash = $passwordEncoder->encodePassword($user, $newPassword);
                $user->setPassword($hash);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_compte_user');
            }
        }

        return $this->render('registration/password-update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profil-user/{id}", name="app_profil_user" )
     */
    public function userProfil(PinRepository $pinRepository, ProfilRepository $profilRepository , $id, UserRepository $userRepository): Response
    {
        
        return $this->render('registration/profil-user.html.twig', [
            'user' =>  $user = $userRepository->findOneById($id),
            'pin' => $pinRepository->findBy(array('user' => $user), array('updatedAt' => 'desc')),
            
        ]); 
    }

    /**
     * @Route("compte-user", name="app_compte_user")
     * 
     */
    public function userCompte(PinRepository $pinRepository, Request $request, ProfilRepository $profilRepository)
    {
        $profil = new profil();
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /**
             * elle permet de supprimer un profil d'utilisateur s'il en existe dans la base de donnéés
             */
            if ($user->isUserProfil($user) === true) {
                $pp = $profilRepository->findBy(['user' => $user ]);
                foreach ($pp as $value) {
                    $entityManager->remove($value);
                }
            }
            $profil->setUser($user);
            $entityManager->persist($profil);
            $entityManager->flush();
           
            return $this->redirectToRoute('app_compte_user');
        }

        return $this->render('registration/compte-user.html.twig', [
            'user' =>  $user = $this->getUser(),
            //retourne l'ensemble des pins d'un user
            'pin' => $pinRepository->findBy(array('user' => $user), array('updatedAt' => 'desc')),
            'form' => $form->createView(),
            //retourne le profil d'un user
            'profil'=> $profilRepository->findOneBy(['user' => $user], ['updatedAt' => 'desc'])
        ]); 

    }


}