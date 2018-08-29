<?php

namespace App\Form;

use App\Entity\ContactRequest;
use App\Form\Type\ReCaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactForm extends AbstractType
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
            ));
            if($options['action'] !== null){
                $builder->setAction($options['action']);
            }

            $builder->add('name',TextType::class,array(
                'label'             =>  'Nom',
                'constraints'       => array(
                    new NotBlank()
                )
            ))
            ->add('firstName',TextType::class,array(
                'label'             =>  'Prénom',
                'constraints'       =>  array(
                    new NotBlank()
                )
            ))
            ->add('adress',TextType::class,array(
                'label' => 'Adresse',
                'mapped'    => false,
                'constraints'   => array(
                    new NotBlank(),
                )
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
            ->add('rgpd',CheckboxType::class,array(
                'label'     => "J'accepte que mes données soient enregistrées.",
                "mapped" => false ,
                'constraints'   => array(
                    new NotBlank(),
                )
            ))

            ->add('submit',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action'     => null,
            'data_class' => ContactRequest::class,
        ]);
    }
}
