<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, array(
                'class' => 'AppBundle:User',
                'choice_label' => 'name',
                'placeholder' => '* Choose user (remove after security)',
            ))
            ->add('name', TextType::class, array(
                    'attr' => array('placeholder' => '* Name (anonymous)'),
                    'required' => false,
                )
            )
            ->add('email', EmailType::class, array(
                    'attr' => array('placeholder' => '* Email (anonymous)'),
                    'required' => false,
                )
            )
            ->add('rating', ChoiceType::class, array(
                'choices' => range(0, 5),
                'choices_as_values' => true,
                'placeholder' => '* Rating'
            ))
            ->add('text', TextareaType::class, array(
                    'attr' => array('placeholder' => '* Your comments')
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Comment',
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "comment";
    }
}