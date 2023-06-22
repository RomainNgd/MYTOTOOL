<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class NewPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastPassword', PasswordType::class, [
                'label' => 'Ancien mot de passe',
                'required' => true,
                'attr' => [
                    'class' => ['form-control']
                ],
                'label_attr' => [
                    'class' => ['form-label']
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez le mot de passe'],
                'attr' => [
                    'class' => ['form-control']
                ],
                'label_attr' => [
                    'class' => ['form-label']
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Réinitialiser',
            ]);
    }

}