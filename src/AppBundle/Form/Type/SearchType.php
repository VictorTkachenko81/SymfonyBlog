<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 22.12.15
 * Time: 18:51
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('param', TextType::class, array(
                'label'         => false,
                'attr'          => array('placeholder' => 'enter search term')
            ))
            ->add('search', SubmitType::class, array(
                    'label'     => 'Search',
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

    public function getBlockPrefix()
    {
        return "search";
    }
}