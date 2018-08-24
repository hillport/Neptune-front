<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility',ChoiceType::class,array(
                'label'     =>  'Civilité',
                'choices'   =>  array(
                    "No Specify"    =>  "Non spécifié",
                    "M"             =>  "Mr",
                    "Mme"           =>  "Mme",
                )
            ))
            ->add('name',TextType::class,array(
                'label'     =>  'Nom'
            ))
            ->add('firstName',TextType::class,array(
                'label'     => 'Prénom'
            ))
            ->add('email',EmailType::class,array(
                'label'         => 'E-mail',
                'constraints'   =>  array(
                    new NotBlank(),
                    new Email()
                )
            ))
            ->add('message',TextareaType::class,array(
                'label'     => 'Message',
                'constraints'   => array(
                    new NotBlank(),
                )
            ))
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
