<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'user.first_name.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'user.first_name.placeholder',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'user.last_name.label',
                'attr' => [
                    'class' => 'form-control form-control-user uppercase',
                    'placeholder' => 'user.last_name.placeholder',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.email.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'user.email.placeholder',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'user.password.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'user.password.placeholder',
                ],
            ])
            ->add('passwordConfirmation', PasswordType::class, [
                'label' => 'user.password_confirmation.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'user.password_confirmation.placeholder',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'translations',
        ]);
    }
}
