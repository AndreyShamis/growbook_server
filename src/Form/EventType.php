<?php

namespace App\Form;

use App\Controller\EventController;
use App\Entity\Event;
use App\Entity\Events\EventHumidity;
use App\Entity\Events\EventTemperature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use App\Twig\AppExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nulTransformer = new CallbackTransformer(
            function ($input)
            {
                return $input;
            },
            function ($input)
            {
                return $input; //null;
            }
        );

//        $builder
//            ->add('andrey_type');
        $builder
            ->add('type', ChoiceType::class, [
//                'widget' => 'single_text',
                'required' => true,
                'attr'=>array(
                    'style'=>'font: 10px;'
                ),
                'choices' => EventController::buildTypeChoices(),
            ]);
//        if (get_class($builder->getData()) === Event::class) {
//            $builder
//                ->add('type', ChoiceType::class, [
////                'widget' => 'single_text',
//                    'required' => true,
//                    'attr'=>array(
//                        'style'=>'font: 10px;'
//                    ),
//                    'choices' => $this->buildTypeChoices(),
//                ]);
//            //$builder->get('type')->addModelTransformer($nulTransformer);
//        } else {
//            $builder
//                ->add('type', HiddenType::class, [
////                'widget' => 'single_text',
//                    'data' => get_class($builder->getData()),
//                    'empty_data' => get_class($builder->getData()),
//                    'required' => false,
//                    'attr'=>array('style'=>'display:none;')
//                ]);
//        }
        $builder
            ->add('value')

            ->add('plant')
            ->add('sensor')
            ->add('note')
        ;
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
