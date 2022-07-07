<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\ModePaiement;
use App\Entity\Operation;
use App\Entity\Tiers;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'required' => true,
            ])
            ->add('modepaiement', EntityType::class, [
                'class' => ModePaiement::class,
                'choice_label' => 'libelle',
            ])
            ->add('tiers', EntityType::class, [
                'class' => Tiers::class,
                'choice_label' => 'libelle',
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libelleList',
            ])
            ->add('montant', NumberType::class, [
                'scale' => 2,
            ])
            ->add('pointe', CheckboxType::class, [
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operation::class,
        ]);
    }
}
