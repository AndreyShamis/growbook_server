<?php

namespace App\Form\Events;

use App\Entity\Events\EventFeed;
use App\Entity\Fertilizer;
use App\Entity\User;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Plant;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventFeedType extends AbstractType
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = null;
        try {
            $user = $this->tokenStorage->getToken()->getUser();
        } catch (\Throwable $ex) {
        }
        $builder
//            ->add('plant','entity', array(
//                'class' => Plant::class,
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('p')
//                        ->where('p.photoPeriod < :photoPeriod')
//                        ->setParameter('photoPeriod', '8')
//                        ->orderBy('p.updatedAt', 'DESC')
//                        ->addOrderBy('p.photoPeriod', 'ASC');
//                }))

            ->add('water', null, ['required' => true, 'empty_data' => '1', 'help' => 'Liters, 100ml=0.1L'])
            ->add('ph', null, ['required' => false, 'empty_data' => '6.0', 'help' => '[0.0-14.0]'])
            ->add('name', null, ['required' => false, 'empty_data' => ''])
            ->add('tds', null, ['required' => true, 'empty_data' => '0', 'help' => '[0-5000]'])
            ->add('ec', null, ['required' => true, 'empty_data' => '0', 'help' => 'TDS * 2'])
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

            if ($user !== null && is_a($user, User::class)) {
                $builder
                    ->add('plant', EntityType::class, array(
                        'class' => Plant::class,
                        'query_builder' => static function(PlantRepository $plantsRepo) use ($user) {
                            $qb = $plantsRepo
                                ->createQueryBuilder('p')
                                ->join('p.owners', 'u')
                                ->where('u.id = :user')
                                ->andWhere('p.photoPeriod < :photoPeriod')
                                ->andWhere('p.finishedAt is NULL')
                                ->setParameter('photoPeriod', '8')
                                ->setParameter('user', $user->getId())
                                ->orderBy('p.updatedAt', 'DESC')
                                ->addOrderBy('p.photoPeriod', 'ASC');
                            return $qb;
                        },
                    ));
            }

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

//            $formModifier = function(FormInterface $form, $fittingStepType) {
//                switch ($fittingStepType) {
//                    case 'SingleValue':
//                        $form->add('fittingStepSingleValue',
//                            'ihear_fittingbundle_fittingstepsinglevaluetype');
//                        if ($form->has('fittingStepDoubleValue'))
//                            $form->remove('fittingStepDoubleValue');
//                        if ($form->has('fittingStepOption'))
//                            $form->remove('fittingStepOption');
//                        break;
//                    case 'DoubleValue':
//                        $form->add('fittingStepDoubleValue',
//                            'ihear_fittingbundle_fittingstepdoublevaluetype');
//                        if ($form->has('fittingStepSingleValue'))
//                            $form->remove('fittingStepSingleValue');
//                        if ($form->has('fittingStepOption'))
//                            $form->remove('fittingStepOption');
//                        break;
//                    case 'Option':
//                        $form->add('fittingStepOption',
//                            'ihear_fittingbundle_fittingstepoptiontype');
//                        if ($form->has('fittingStepSingleValue'))
//                            $form->remove('fittingStepSingleValue');
//                        if ($form->has('fittingStepDoubleValue'))
//                            $form->remove('fittingStepDoubleValue');
//                        break;
//                }
//            };
//
//            $builder->addEventListener(
//                FormEvents::PRE_SET_DATA,
//                function(FormEvent $event) use ($formModifier) {
//                    $form = $event->getForm();
//                    // this is the FittingStep
//                    $data = $event->getData();
//                    // this is the Entity that contains the value(s)
//                    // i.e. FittingStepSingleValue
//                    $fittingStepType = $data->getFittingStepType();
//                    switch ($fittingStepType) {
//                        case 'SingleValue':
//                            $formModifier($form, $fittingStepType);
//                            break;
//                        case 'DoubleValue':
//                            $formModifier($form, $fittingStepType);
//                            break;
//                        case 'Option':
//                            $formModifier($form, $fittingStepType);
//                            break;
//                    }
//                }
//            );
//            $formModifier = function (FormInterface $form, EventFeed $eventFeed = null) {
//                $positions = null ;//=== $eventFeed ? [] : $eventFeed->getAvailablePositions();
//
//                $form->add('plant', EntityType::class, [
//                    'class' => 'App\Entity\Plant',
//                    'placeholder' => '',
//                    'choices' => $positions,
//                ]);
//            };
//
//            $builder->get('plant')->addEventListener(FormEvents::PRE_SET_DATA,
//                function (FormEvent $event) use ($formModifier) {
//                    // It's important here to fetch $event->getForm()->getData(), as
//                    // $event->getData() will get you the client data (that is, the ID)
//                    $sport = $event->getForm();
//                    //$sport = $event->getForm()->getData();
//
//                    // since we've added the listener to the child, we'll have to pass on
//                    // the parent to the callback functions!
//                    $formModifier($event->getForm()->getParent(), $sport);
//                }
//            );
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
