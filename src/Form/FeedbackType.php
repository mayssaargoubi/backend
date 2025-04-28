<?php

namespace App\Form;

use App\Entity\Feedback;
use App\Entity\Evaluation;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('evaluation', EntityType::class, [
                'class' => Evaluation::class,
                'choice_label' => 'id', // Utilise l'ID ou un autre champ pour l'affichage
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
            ])
            ->add('commentaire')
            ->add('feedback')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
