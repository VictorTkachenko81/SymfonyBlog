<?php

namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class BlogController extends Controller
{

    //ToDo: fix page:0

    /**
     * @param $page
     * @Method("GET")
     * @Route("/{pager}/{page}", name="homepage",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "\d+",
     *     })
     * @Template("AppBundle:frontend:index.html.twig")
     *
     * @return Response
     */
    public function indexAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository("AppBundle:Article")
            ->getArticlesWithCountComment($page);

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
     * @param $page
     * @Method("GET")
     * @Route("/{sortBy}/{param}/{pager}/{page}", name="sortArticles",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "sortBy": "category|tag|author|date",
     *          "pager": "page",
     *          "page": "\d+",
     *     })
     * @Template("AppBundle:frontend:index.html.twig")
     *
     * @return Response
     */
    public function sortAction($sortBy, $param, $page = 1)
    {
        if ($sortBy == 'date') {
            $dateConstraint = new Assert\Date();
            $dateConstraint->message = 'Invalid date';

            $errorList = $this->get('validator')->validate($param, $dateConstraint);

            if (0 !== count($errorList)) {
                throw $this->createNotFoundException(
                    $errorMessage = $errorList[0]->getMessage()
                );
            }
        }

        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository("AppBundle:Article")
            ->getArticlesSorted($sortBy, $param, $page);

        return [
            'articles' => $articles,
        ];
    }

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

    /**
     * @Template("AppBundle:frontend:widgetTabs.html.twig")
     *
     * @return Response
     */
    public function getTabsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $popularArticles = $em->getRepository("AppBundle:Article")
            ->getPopularArticles(5);

        $recentArticles = $em->getRepository("AppBundle:Article")
            ->getRecentArticles(5);

        $recentComments = $em->getRepository("AppBundle:Comment")
            ->getRecentComments(5);

        return [
            'popularArticles' => $popularArticles,
            'recentArticles'  => $recentArticles,
            'recentComments'  => $recentComments,
        ];
    }
}
