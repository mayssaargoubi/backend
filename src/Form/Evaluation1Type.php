<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class Evaluation1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('manager', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'manager');
                },
                'label' => 'Nom du Manager',
                'placeholder' => 'Choisir un manager',
            ])
            ->add('employee', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'employee');
                },
                'label' => 'Nom de l\'Employé',
                'placeholder' => 'Choisir un employé',
            ])
            ->add('dateEvaluation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'évaluation',
            ])
            ->add('periode', ChoiceType::class, [
                'choices' => [
                    'Semestriel' => 'semestriel',
                    'Annuel' => 'annuel',
                ],
                'label' => 'Période',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en attente',
                    'Validé' => 'valide',
                    'Refusé' => 'refusé',
                ],
                'label' => 'Statut',
            ])
            ->add('commentaire', TextareaType::class, [
                'required' => false,
                'label' => 'Commentaire',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
