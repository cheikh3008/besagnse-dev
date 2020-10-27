<?php

namespace App\Form;

use App\Entity\Pin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'required' => true,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_uri' => false,
                'imagine_pattern' => 'squared_thumbnail_small'
                ])
            ->add('titre')
            ->add('description')
            ->add('categorie', ChoiceType::class, [
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Aucun',
                'choices'  => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                    'Enfant' => 'enfant',
                    'Tissu' => 'tissu' ,
                    'Daalou Ngaay' => 'daalou', 
                ] ,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pin::class,
        ]);
    }
}