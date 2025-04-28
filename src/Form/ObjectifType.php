<?php

namespace App\Form;

use App\Entity\Objectif;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ObjectifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateCreation', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateEcheance', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('manager', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                'label' => 'Manager',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'manager');
                },
                'placeholder' => 'Choose a manager',
            ])
            ->add('employee', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                'label' => 'Employee',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'employee');
                },
                'placeholder' => 'Choose an employee',
            ]);
        // Note: Le champ "poid" est exclu (comme demandé)
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objectif::class,
        ]);
    }
}
