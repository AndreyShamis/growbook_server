<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
        ;

        if (array_key_exists('edit_enabled', $options) && $options['edit_enabled'] === true && $options['can_change_permissions'] === true) {
            $builder
                ->add('isActive')
                ->add('roles', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true, // render check-boxes
                    'choices' => [
                        'Super Admin' => 'ROLE_SUPER_ADMIN',
                        'Admin' => 'ROLE_ADMIN',
                        'Manager' => 'ROLE_MANAGER',
                        'USER' => 'ROLE_USER',
                        // ...
                    ],
                ])
            ;
        }

        //if($options["data"] === $options['current_user']){
        /** @var User $edited_user */
        $edited_user = $options['data'];
        if(!$edited_user->isLdapUser()){
            $builder->add('firstName', TextType::class);
            $builder->add('lastName', TextType::class);
            $builder->add('plainPassword', RepeatedType::class, array(
                'required' => false,
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ));
        }
        // }
    }

    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(array(
            'edit_enabled'
        ));

        $resolver->setDefaults(array(
            'data_class' => User::class,
            'edit_enabled' => false,
            'current_user' => null,
            'can_change_permissions' => false,
        ));
    }

}
