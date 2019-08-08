<?php

namespace App\Form\Events;

use App\Entity\Events\EventFeed;
use App\Entity\Fertilizer;
use App\Repository\FertilizerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFeedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plant')
            ->add('water', null, ['required' => true, 'empty_data' => '1', 'help' => 'Liters, 100ml=0.1L'])
            ->add('ph', null, ['required' => false, 'empty_data' => '6.0', 'help' => '[0.0-14.0]'])
            ->add('name', null, ['required' => false, 'empty_data' => ''])
            ->add('tds')
            ->add('ec')
            ->add('temperature')
            ->add('happenedAt')
//            ->add('updatedAt')
//            ->add('type')
//            ->add('value')
            ->add('note', null, ['required' => false, 'empty_data' => ''])
            ->add('value1')
            ->add('value2')
            ->add('value3')
//            ->add('add_fertilizer', ButtonType::class, [
//                'attr' => ['class' => 'Add fertilizer'],
//            ])
            ;
            $builder
                ->add('fertilizers', EntityType::class, [
                    // Multiple selection allowed
                    'multiple' => false,
                    // Render as checkboxes
                    'expanded' => false,
                    // This field shows all the categories
                    'class'    => Fertilizer::class,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'fertilizers'
                    ]
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //


        $resolver->setDefaults([
            'data_class' => EventFeed::class,
   //         'fertiRepo' => null,
        ]);
//        $resolver->setRequired(array(
//            'fertiRepo'
//        ));
        //$resolver->setAllowedTypes('fertiRepo', array('App\Repository\FertilizerRepository'));
    }
}
