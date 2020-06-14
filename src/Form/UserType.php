<?php

namespace App\Form;

use App\Entity\User;
use App\Service\FileUploader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @var FileUploader $fileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', FileType::class, [
                'label' => 'user.avatar.label',
                'attr' => [
                    'placeholder' => 'user.avatar.placeholder',
                ],
                'required' => !$options['edit_mode'],
                'data' => null,
            ])
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
                'disabled' => $options['edit_mode'],
                'label' => 'user.email.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'user.email.placeholder',
                ],
            ])
            ->add('password', PasswordType::class, [
                'required' => !$options['edit_mode'],
                'label' => 'user.password.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'user.password.placeholder',
                ],
            ])
            ->add('passwordConfirmation', PasswordType::class, [
                'required' => !$options['edit_mode'],
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
            'edit_mode' => false,
        ]);
    }

}
