<?php

namespace App\Form\Events;

use App\Entity\Events\EventTemperature;
use App\Form\EventType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTemperatureType extends EventType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('temperature')
//            ->add('plant')
//            ->add('sensor')
        ;
        parent::buildForm($builder, $options);
        $builder->remove('plant');
        $builder->remove('value');
        //$builder->remove('type');

    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => EventTemperature::class,
//        ]);
//    }
}
