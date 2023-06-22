<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'email',
                'attr'=> [
                    'class' => 'form-control my-2'
                ],
                'label_attr' => [
                    'class' =>'form-label fw-bold mt-3'
                ],
                'required' => true,
                'invalid_message' => 'Le mail est incorrect'
            ])
            ->add('last', PasswordType::class, [
                'required' => true,
                'label' => 'mot de passe précédent',
                'attr'=> [
                    'class' => 'form-control my-2'
                ],
                'label_attr' => [
                    'class' =>'form-label fw-bold mt-3'
                ],

            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'le mot de passe et la confirmation doivent être identique',
                'required' => true,
                'first_options' => [
                    'label' => 'mot de passe',
                    'attr' => [
                        'placeholder' => 'Merci de saisir le mot de passe',
                        'class' =>'form-control my-2'
                    ],
                    'label_attr'=>[
                        'class' => 'form-label fw-bold mt-3'
                    ]
                ],
                'second_options' => [
                    'label' => 'confirmation mot de passe',
                    'attr' => [
                        'placeholder' => 'Merci de confirmer le mot de passe',
                        'class' =>'form-control my-2'
                    ],
                    'label_attr'=>[
                        'class' => 'form-label fw-bold mt-3'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Modifié mon profil",
                'attr' => [
                    'class' => 'btn btn-primary mt-2'
                ]
            ]);
    }

}