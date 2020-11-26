<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('header', HeaderType::class, []);
        $builder->add('file', FileType::class, [
            'label' => 'Импорт csv',
            'required' => false
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use($options)
            {
                $data = $event->getData();
                $form = $event->getForm();
                $i = -1;
                do{
                    $i++;
                }
                while(array_key_exists('product'.$i, $data));
                $quantity = $i;

                if($options['add'])
                    $quantity++;

                for ($i = 0; $i < $quantity; $i++)
                {
                    $form->add('product'.$i, SupplyType::class, [
                        'label' => false,
                    ]);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
        $resolver->setDefined('add');
    }
}
