<?php

namespace App\Form;

use App\Entity\Description;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enName', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-1',
                    'placeholder' => 'Заголовок группы'
                ],
                'label' => 'Английский',
                'required' => false,
            ])
            ->add('enDes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Универсальное описание'
                ],
                'label' => false,
                'required' => false,
            ])
            ->add('plName', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-1',
                    'placeholder' => 'Заголовок группы'
                ],
                'label' => 'Польский',
                'required' => false
            ])
            ->add('plDes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Универсальное описание'
                ],
                'label' => false,
                'required' => false
            ])
            ->add('frName', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-1',
                    'placeholder' => 'Заголовок группы'
                ],
                'label' => 'Французский',
                'required' => false
            ])
            ->add('frDes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Универсальное описание'
                ],
                'label' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Description::class,
        ]);
    }
}
