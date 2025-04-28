<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Objectif;
use App\Entity\ObjectifNote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ObjectifNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', NumberType::class, [
                'required' => true,
            ])
            ->add('commentaire', TextType::class, [
                'required' => false,
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Attente' => 'attente',
                    'Partiellement atteint' => 'partiellement',
                    'Atteint' => 'atteint',
                ],
            ])
            ->add('objectif', EntityType::class, [
                'class' => Objectif::class,
                'choice_label' => 'titre',
                'placeholder' => 'Sélectionnez un objectif',
            ])
            ->add('evaluation', EntityType::class, [
                'class' => Evaluation::class,
                'choice_label' => 'id',
                'placeholder' => 'Sélectionnez une évaluation',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObjectifNote::class,
        ]);
    }
}
