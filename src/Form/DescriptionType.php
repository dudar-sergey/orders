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
                    'class' => 'form-control mb-1'
                ],
                'label' => 'Английский'
            ])
            ->add('enDes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'label' => false,
            ])
            ->add('plName', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-1'
                ],
                'label' => 'Польский'
            ])
            ->add('plDes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'label' => false,
            ])
            ->add('frName', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-1'
                ],
                'label' => 'Французский'
            ])
            ->add('frDes', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'label' => false,
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
