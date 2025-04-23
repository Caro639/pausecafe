<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Promo;
use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderComfirmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom'
                ],
                'label' => 'Nom',
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 100])
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Adresse'
                ],
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\Length(['min' => 5, 'max' => 255])
                ]
            ])
            ->add('zipcode', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Code postal'
                ],
                'label' => 'Code postal',
                'constraints' => [
                    new Assert\Length(['min' => 5, 'max' => 5])
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ville'
                ],
                'label' => 'Ville',
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150])
                ]
            ])
            // ->add('promo', EntityType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //         'placeholder' => 'Code promo'
            //     ],
            //     'class' => Promo::class,
            //     'choice_label' => 'id',
            // ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-lg btn-dark mt-4'
                ],
                'label' => 'Valider la commande'
            ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
