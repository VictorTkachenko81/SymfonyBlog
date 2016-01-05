<?php

namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/", name="homepage")
     * @Template("AppBundle:frontend:index.html.twig")
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository("AppBundle:Article")
            ->getArticlesWithCountComment();

        return [
            'articles' => $articles,
        ];
    }

    /**
     * @param $slug
     * @Method("GET")
     * @Route("/article/{slug}", name="showArticle")
     * @Template("AppBundle:frontend:show.html.twig")
     *
     * @return Response
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository("AppBundle:Article")
            ->getArticleWithCountComment($slug);

        return [
            'article' => $article,
        ];
    }

    /**
     * @param $sortBy
     * @param $param
     * @Method("GET")
     * @Route("/{sortBy}/{param}", name="sortArticles", requirements={
     *     "sortBy": "category|tag|author|date"
     *     })
     * @Template("AppBundle:frontend:index.html.twig")
     *
     * @return Response
     */
    public function sortAction($sortBy, $param)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository("AppBundle:Article")
            ->getArticlesSorted($sortBy, $param);

        return [
            'articles' => $articles,
        ];
    }
//    ToDo: need add sort by date

    /**
     * @Template("AppBundle:frontend:widgetTags.html.twig")
     *
     * @return Response
     */
    public function getTagsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository("AppBundle:Tag")
            ->getTagsWithCount();

        shuffle($tags);

        return [
            'tags' => $tags,
        ];
    }

    /**
     * @Template("AppBundle:frontend:widgetCategories.html.twig")
     *
     * @return Response
     */
    public function getCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("AppBundle:Category")
            ->getCategoriesWithCount();

        return [
            'categories' => $categories,
        ];
    }
}
