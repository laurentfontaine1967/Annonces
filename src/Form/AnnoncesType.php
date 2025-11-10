<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Annonces;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class AnnoncesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
           ->add('titre', TextType::class, [
        'label' => 'Titre',
        'constraints' => [
            new Assert\NotBlank([
                'message' => 'Le titre ne peut pas être vide.',
            ]),
            new Assert\Length([
                'min' => 10,
                'max' => 50,
                'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères.',
                'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.',
            ]),
        ],
    ])

              ->add('description', TextType::class, [
        'label' => 'description',
        'constraints' => [
            new Assert\NotBlank([
                'message' => 'La descrition ne peut pas être vide.',
            ]),
            new Assert\Length([
                'min' => 50,
                'max' => 250,
                'minMessage' => 'La descrition doit contenir au moins {{ limit }} caractères.',
                'maxMessage' => 'La descrition ne peut pas dépasser {{ limit }} caractères.',
            ]),
        ],
    ])

            ->add('prix')
            
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
             ->add('imageFile', FileType::class, [
        'required' => false,
        'mapped' => true, // on laisse à true car Vich gère l'entité (le champ n'est pas ORM mais Vich s’en sert)
        'constraints' => [
            new FileConstraint([
                'maxSize' => '5M',
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
            ]),
        ],
        'label' => 'Photo (jpg, png, webp)',
    ]);
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
