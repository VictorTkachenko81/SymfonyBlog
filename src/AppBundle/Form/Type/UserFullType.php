<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFullType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isActive', ChoiceType::class, array(
                'choices'   => array(
                    'Yes'   => true,
                    'No'    => false,
                ),
                'choices_as_values' => true,
            ))
            ->add('username', TextType::class, array(
                'attr'          => array('placeholder' => '* Username')
            ))
            ->add('roleObjects', EntityType::class, array(
                'class'         => 'AppBundle:Role',
                'choice_label'  => 'name',
                'placeholder'   => '* Role',
                'multiple'      => 'true',
            ))
            ->add('file', FileType::class, array(
                'attr'      => array('placeholder' => '* Photo')
            ))
            ->add('email', EmailType::class, array(
                'attr'      => array('placeholder' => '* Email')
            ))
            ->add('password', RepeatedType::class, array(
                'type'                  => PasswordType::class,
                'invalid_message'       => 'The password fields must match.',
                'options'               => array('attr' => array('class' => 'password-field')),
                'required'              => true,
                'first_options'         => array(
                    'attr'              => array('placeholder' => '* Password'
                    )),
                'second_options'        => array(
                    'attr'              => array(
                        'placeholder'   => '* Repeat Password'
                    )),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => 'AppBundle\Entity\User',
            'em'            => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "user";
    }
}