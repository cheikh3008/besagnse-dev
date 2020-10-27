<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    private $role;
    public function __construct(RoleRepository $role)
    {
        $this->role = $role;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $role = $this->role->findBy(array('libelle' => ["ROLE_TAILLEUR", "ROLE_VISITEUR"]));
        $builder
            ->add('prenom', TextType::class, ['attr' => ['placeholder' => 'Tapez votre prénom ']])
            ->add('nom', TextType::class, ['attr' => ['placeholder' => 'Tapez votre nom ']])
            ->add('username', TextType::class, ['attr' => ['placeholder' => 'Tapez votre username ']])
            ->add('telephone', NumberType::class, ['attr' => ['placeholder' => 'Tapez votre numéro de téléphone ']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe',  'attr' => ['placeholder' => 'Tapez le mot de passe ']],
                'second_options' => ['label' => 'Confirmation de mot passe',  'attr' => ['placeholder' => 'Retapez le mot de passe']],
                'invalid_message' => 'Les deux mot de passe ne correspondent pas.',
                
            ])
            ->add('nomEntreprise', TextType::class, ['attr' => ['placeholder' => 'Tapez votre nom de d\'entreprise ']])
            ->add('adresse', TextType::class, ['attr' => ['placeholder' => 'Tapez votre adresse ']])
            ->add('role', EntityType::class,['class' => Role::class,'choice_label' => 'libelle',
            'placeholder' => 'Choisir un role', 'choices'=> $role])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
