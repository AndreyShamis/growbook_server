<?php

namespace App\Form\Events;

use App\Entity\Events\EventHumidity;
use App\Form\EventType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventHumidityType extends EventType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('humidity');
        parent::buildForm($builder, $options);
        $builder->remove('plant');
        $builder->remove('value');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventHumidity::class,
        ]);
    }
}
