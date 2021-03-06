<?php

namespace App\Form;

use App\Entity\Plant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            //->add('createdAt')
            //->add('updatedAt')
            ->add('uniqId')
            ->add('startedAt')
            ->add('finishedAt')
            ->add('soilMedium')
            ->add('photoPeriod',null, ['required' => false, 'empty_data' => '0', 'help' => Plant::getPhotoPeriodListAsHelp()])
            ->add('owners')
            ->add('prefloweredAt')
            ->add('floweredAt')
            ->add('resetCounter')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
