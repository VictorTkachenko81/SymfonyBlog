<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 08.01.16
 * Time: 18:45
 */

namespace AppBundle\Twig;


use AppBundle\Form\Type\SearchType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;

class AppExtension extends \Twig_Extension
{
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('popularArticlesTabs',
                array($this, 'getTabs'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('tagsList',
                array($this, 'getTags'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('categoriesList',
                array($this, 'getCategories'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'))
            ),
            new \Twig_SimpleFunction('searchForm',
                array($this, 'getSearchForm'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'))
            )

        );
    }

    public function getTabs(\Twig_Environment $twig)
    {
        $em = $this->doctrine->getManager();
        $popularArticles = $em->getRepository("AppBundle:Article")
            ->getPopularArticles(5);

        $recentArticles = $em->getRepository("AppBundle:Article")
            ->getRecentArticles(5);

        $recentComments = $em->getRepository("AppBundle:Comment")
            ->getRecentComments(1, 5);

        return $twig->render(
            'AppBundle:blog:widgetTabs.html.twig',
            array(
                'popularArticles' => $popularArticles,
                'recentArticles' => $recentArticles,
                'recentComments' => $recentComments,
            )
        );
    }

    public function getTags(\Twig_Environment $twig)
    {
        $em = $this->doctrine->getManager();
        $tags = $em->getRepository("AppBundle:Tag")
            ->getTagsWithCount();

//        shuffle($tags);

        return $twig->render(
            'AppBundle:blog:widgetTags.html.twig',
            array(
                'tags' => $tags,
            )
        );
    }

    public function getCategories(\Twig_Environment $twig)
    {
        $em = $this->doctrine->getManager();
        $categories = $em->getRepository("AppBundle:Category")
            ->getCategoriesWithCount();

        return $twig->render(
            'AppBundle:blog:widgetCategories.html.twig',
            array(
                'categories' => $categories,
            )
        );
    }

    public function getSearchForm(\Twig_Environment $twig)
    {
        $kernel = $GLOBALS['kernel'];
        $form = $kernel->getContainer()->get('form.factory')->createBuilder(SearchType::class)->getForm();

        return $twig->render(
            'AppBundle:blog:widgetSearchForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function getName()
    {
        return 'app_extension';
    }
}