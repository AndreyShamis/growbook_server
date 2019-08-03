<?php

namespace App\Form;

use App\Entity\NodeCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeCommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cmd_key')
            ->add('cmd_value')
            ->add('published')
            ->add('received')
            ->add('can_be_deleted')
            ->add('plant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NodeCommand::class,
        ]);
    }
}
