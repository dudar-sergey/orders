<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstProduct', EntityType::class, [
                'class' => Product::class,
                'attr' => [
                    'class' => 'form-control chosen-select'
                ],
                'label' => 'Товары для объединения',
                'multiple' => true
            ])
            ->add('article', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Артикул'
            ])
            ->add('price', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Цена'
            ])
            ->add('quantity', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Количество комплектов'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
