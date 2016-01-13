<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/", name="mainAdmin")
     * @Template("AppBundle:admin:main.html.twig")
     *
     * @return Response
     */
    public function indexAction()
    {
        return [];
    }

    //ToDo: fix page over limit
    /**
     * @param $page
     * @Method("GET")
     * @Route("/articles/{pager}/{page}", name="articlesAdmin",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *     })
     * @Template("AppBundle:admin:articles.html.twig")
     *
     * @return Response
     */
    public function articlesAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository("AppBundle:Article")
            ->getArticlesWithCountComment($page);

        return [
            'articles' => $articles,
        ];
    }

    /**
     * @Route("/success", name="successAdmin")
     * @Template("AppBundle:blog:success.html.twig")
     *
     * @return Response
     */
    public function successAction()
    {
        return [];
    }

}
