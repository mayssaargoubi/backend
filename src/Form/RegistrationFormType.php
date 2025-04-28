<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)  // Champ prénom
            ->add('lastname', TextType::class)   // Champ nom de famille
            ->add('email', EmailType::class)      // Champ email
            ->add('password', PasswordType::class)  // Champ mot de passe
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Employee' => 'employee',  // Option pour "employee"
                    'Manager' => 'manager',    // Option pour "manager"
                ],
                'mapped' => false, // Le champ n'est pas mappé directement à l'entité, il sera récupéré dans le contrôleur
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,  // L'entité associée au formulaire est User
            'csrf_protection' => false,  // Désactiver la protection CSRF pour ce formulaire
        ]);
    }
}
