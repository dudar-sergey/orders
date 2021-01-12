<?php

namespace App\Form;

use App\Entity\Description;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManyDescriptionType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $descriptions = $this->em->getRepository(Description::class)->findAll();
        foreach ($descriptions as $description)
        {
            $childName = 'description'.$description->getId();
            $builder
                ->add($childName, DescriptionType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
