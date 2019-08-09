<?php

namespace App\Form;

use App\Entity\CustomField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('property')
            ->add('propertyValue')
            ->add('propertyValueString')
            ->add('object_host_id')
            ->add('object_host_type')
            ->add('created_at')
            ->add('updated_at')
            ->add('propertyValueType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomField::class,
        ]);
    }
}
