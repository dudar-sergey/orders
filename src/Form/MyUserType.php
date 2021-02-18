<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                  'class' => 'form-control mb-3'
                ],
                'label' => false
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Администратор' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'attr' => [
                    'class' => 'mt-3'
                ]
            ])
            ->add('allegroUserToken', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'label' => 'Client id из аллегро'
            ])
            ->add('allegroApplicationToken', TextAreaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'label' => 'client secret из аллегро'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
