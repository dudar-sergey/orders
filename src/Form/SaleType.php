<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'label' => false,
                'attr' => [
                  'class' => 'form-control',
                ],
            ])
            ->add('platform', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('orderNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('createAt', DateTimeType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Дата',
            ])
            ->add('quantity', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('price', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('purchase', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('currency', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-success mt-3',
                ],
                'label' => 'Submit',
            ])

        ;

        /*$builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event)
            {
                $data = $event->getData();
                $form = $event->getForm();
            }
        );*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
