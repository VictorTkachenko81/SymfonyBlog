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

class UserPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
        return "userPassword";
    }
}