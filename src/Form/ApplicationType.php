<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Election;
use App\Service\ElectionService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    private $electionService;

    public function __construct(ElectionService $electionService)
    {
        $this->electionService = $electionService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('election', HiddenType::class)
            ->add('content', CKEditorType::class, [
                'label' => ' ',
                'config' => [
                    'height' => '380px',
                    'uiColor' => '#e2e2e2',
                    'toolbar' => 'full',
                    'required' => true,
                ]
            ]);
        $builder->get('election')->addModelTransformer(
            new CallbackTransformer(
                function (Election $election) {
                    return $election->getId();
                },
                function (string $electionId) {
                    return $this->electionService->getElectionById(intval($electionId));
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'user_application',
            'csrf_field_name' => 'token',
            'method' => 'POST',
            'translation_domain' => 'translations',
        ]);
    }

}
