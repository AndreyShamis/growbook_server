<?php

namespace App\Form;

use App\Controller\EventController;
use App\Entity\Event;
use App\Entity\Events\EventHumidity;
use App\Entity\Events\EventTemperature;
use App\Model\TypeEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use App\Twig\AppExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


//        $builder
//            ->add('andrey_type');
//        $builder
//            ->add('type', ChoiceType::class, [
////                'widget' => 'single_text',
//                'required' => true,
//                'attr'=>array(
//                    'style'=>'font: 10px;'
//                ),
//                'choices' => EventController::buildTypeChoices(),
//            ]);
        $builder
            ->add('value')

            ->add('plant')
            ->add('sensor')
            ->add('note')
            ->add('name', TextType::class, ['required' => false, 'empty_data' => ''])

        ;
        $builder->add('type', ChoiceType::class , TypeEvent::buildFormType());

//        if ($options['data'] !== null && $options['data']->getType() !== null) {
//            $builder->add('type', ChoiceType::class , TypeEvent::buildFormType(array($options['data']->getType())));
//        } else {
//            $builder->add('type', ChoiceType::class , TypeEvent::buildFormType());
//        }
//        $builder
//            ->add('name', TextType::class, [
////                'widget' => 'single_text',
//                'data' => '',
//                'empty_data' => '',
//                'required' => false
//            ])
//            //->add('createdAt')
//            //->add('updatedAt')
//        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // ... adding the name field if needed
            if ('a' === 'a' && true) {
                $a = 1;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => false,
        ]);
        $resolver->setDefaults(array(
            'name' => ''
        ));
    }
}
