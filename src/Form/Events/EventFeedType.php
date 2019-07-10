<?php

namespace App\Form\Events;

use App\Entity\Events\EventFeed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFeedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plant')
            ->add('water',null, ['required' => true, 'empty_data' => '1', 'help' => 'Liters, 100ml=0.1L'])
            ->add('ph', null, ['required' => false, 'empty_data' => '6.0', 'help' => '[0.0-14.0]'])
            ->add('tds')
            ->add('ec')
            ->add('temperature')
            ->add('name', null, ['required' => false, 'empty_data' => ''])
//            ->add('createdAt')
//            ->add('updatedAt')
//            ->add('type')
//            ->add('value', null, ['required' => false])
            ->add('note', null, ['required' => false, 'empty_data' => ''])
            ->add('value1')
            ->add('value2')
            ->add('value3')
//            ->add('ip')

//            ->add('sensor')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventFeed::class,
        ]);
    }
}
