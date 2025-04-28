<?php




namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('manager', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'label' => 'Manager',
                'required' => true,
                'query_builder' => fn($er) => $er->createQueryBuilder('u')->where('u.role = :role')->setParameter('role', 'manager'),
            ])
            ->add('employee', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'label' => 'Employé',
                'required' => true,
                'query_builder' => fn($er) => $er->createQueryBuilder('u')->where('u.role = :role')->setParameter('role', 'employee'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
