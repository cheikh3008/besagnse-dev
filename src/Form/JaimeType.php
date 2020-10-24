<?php

namespace App\Form;

use App\Entity\Jaime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JaimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('pin', EntityType::class, ['class' => Pin::class])
            // ->add('user', EntityType::class, ['class' => User::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Jaime::class,
        ]);
    }
}
