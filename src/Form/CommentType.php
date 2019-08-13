<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentType extends AbstractType
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
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('happenedAt')
            ->add('commentName')
            ->add('comment')
//            ->add('enabled')
//            ->add('user')
//            ->add('plant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
