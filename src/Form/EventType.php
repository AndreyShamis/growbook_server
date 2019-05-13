<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
//                'widget' => 'single_text',
                'data' => '',
                'empty_data' => '',
                'required' => false
            ])
            //->add('createdAt')
            //->add('updatedAt')
            ->add('type', HiddenType::class, [
//                'widget' => 'single_text',
                'data' => get_class($builder->getData()),
                'empty_data' => get_class($builder->getData()),
                'required' => false,
                'attr'=>array('style'=>'display:none;')
            ])
            ->add('value')
            ->add('note')
            ->add('plant')
            ->add('sensor')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
        $resolver->setDefaults(array(
            'name' => ''
        ));
    }
}
