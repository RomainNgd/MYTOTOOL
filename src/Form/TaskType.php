<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom :',
                'label_attr' => [
                    'class' => 'form-label fw-bold mt-3'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'max' => 80,
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('note', TextareaType::class, [
                'required' => true,
                'label' => 'Description :',
                'label_attr' => [
                    'class' => 'form-label fw-bold mt-3'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('priority', ChoiceType::class, [
                'required' => true,
                'label' => 'Priorité :',
                'label_attr' => [
                    'class' => 'form-label fw-bold mt-3'
                ],
                'attr' => [
                    'class' => 'form-select'
                ],
                'choices' => [
                    'Faible' => 1,
                    'Moyenne' => 2,
                    'Haute' => 3,
                    'Urgent' => 4,
                ]
            ])
            ->add('deadline', DateType::class, [
                'required' => true,
                'label' => 'Deadline',
                'label_attr' => [
                    'class' => 'form-label fw-bold mt-3'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'date'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary mt-5'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
