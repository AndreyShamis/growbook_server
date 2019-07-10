<?php

namespace App\Form\Events;

use App\Entity\Events\EventFeed;
use App\Entity\Events\EventFertilize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFertilizeType extends EventFeedType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('fertilizer');
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventFertilize::class,
        ]);
    }
}
