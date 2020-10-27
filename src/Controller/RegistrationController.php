<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationFormType;
use App\Repository\PinRepository;
use Symfony\Component\Form\FormError;
use App\Security\FormLoginAuthenticator;
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
            return $this->redirectToRoute('app_home');
        }
        return $this->render('registration/edit-profil.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/password-update", name="app_password-update")
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
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('registration/password-update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/my-account", name="app_my_account")
     */
    public function myAccount(PinRepository $pinRepository): Response
    {
        return $this->render('registration/my-account.html.twig', [
            'user' =>  $user = $this->getUser(),
            'pins' => $pinRepository->findBy(array('user' => $user), array('updatedAt' => 'desc')),
        ]); 
    }
}