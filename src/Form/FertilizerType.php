<?php

namespace App\Form;

use App\Entity\Fertilizer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FertilizerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('N')
            ->add('P')
            ->add('K')
            ->add('description',TextareaType::class, ['required' => false, 'empty_data' => ''])
            ->add('additionalElements',TextareaType::class, ['required' => false, 'empty_data' => ''])
            ->add('company',TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('dose',TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('content', null, ['help' => 'liters'])
            ->add('url1',TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('url2',TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('url3',TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('price',TextType::class, ['required' => false, 'empty_data' => '0'])
            ->add('target',TextType::class, ['required' => false, 'empty_data' => 'UNI', 'help' => 'UNI/COCO/SOIL/HYDRO']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fertilizer::class,
        ]);
    }
}
