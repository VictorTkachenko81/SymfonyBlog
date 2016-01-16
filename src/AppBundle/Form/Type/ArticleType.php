<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, array(
                'class'         => 'AppBundle:User',
                'choice_label'  => 'name',
                'placeholder'   => '* Choose user (remove after security)',
            ))
            ->add('title', TextType::class, array(
                'label'         => false,
                'attr'          => array('placeholder' => '* Title')
            ))
            ->add('file', FileType::class, array(
                'label'         => false,
                'attr'          => array('placeholder' => '* Picture')
            ))
            ->add('categories', EntityType::class, array(
                'class'         => 'AppBundle:Category',
                'choice_label'  => 'name',
                'placeholder'   => '* Choose category',
                'multiple'      => 'true',
            ))
            ->add('tags', EntityType::class, array(
                'class'         => 'AppBundle:Tag',
                'choice_label'  => 'name',
                'placeholder'   => '* Choose tags',
                'multiple'      => 'true',
            ))
            ->add('text', TextareaType::class, array(
                'attr'          => array('placeholder' => '* Your article text')
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => 'AppBundle\Entity\Article',
            'em'            => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "article";
    }
}