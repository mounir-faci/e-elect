<?php

namespace App\Form;

use App\Entity\Election;
use App\Entity\User;
use App\Service\FileUploader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElectionType extends AbstractType
{
    private const ELECTION_STATUS_CHOICES = [
        'election.status.created' => Election::STATUS_CREATED,
        'election.status.pending' => Election::STATUS_PENDING,
        'election.status.finished' => Election::STATUS_FINISHED,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'election.name.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'election.name.placeholder',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'election.description.label',
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'election.description.placeholder',
                ],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'election.start_date.label',
                'attr' => [
                    'class' => 'form-control form-control-user datepicker',
                    'placeholder' => 'election.start_date.placeholder',
                ],
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'election.end_date.label',
                'attr' => [
                    'class' => 'form-control form-control-user datepicker',
                    'placeholder' => 'election.end_date.placeholder',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'election.status.label',
                'choices' => self::ELECTION_STATUS_CHOICES,
                'attr' => [
                    'class' => 'custom-select',
                    'placeholder' => 'election.status.placeholder',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Election::class,
            'translation_domain' => 'translations',
        ]);
    }

}
