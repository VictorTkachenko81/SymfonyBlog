<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @param $page
     * @Method("GET")
     * @Route("/comments/{pager}/{page}", name="commentsAdmin",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *     })
     * @Template("AppBundle:admin:comments.html.twig")
     *
     * @return Response
     */
    public function commentsAction($page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository("AppBundle:Comment")
            ->getRecentComments($page, 10);

        return [
            'comments' => $comments,
        ];
    }

    /**
     * @param $page
     * @Method("GET")
     * @Route("/users/{pager}/{page}", name="usersAdmin",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *     })
     * @Template("AppBundle:admin:users.html.twig")
     *
     * @return Response
     */
    public function usersAction($page = 1)
    {
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        return [
            'users' => $users,
        ];
    }

    /**
     * @param $page
     * @Method("GET")
     * @Route("/roles/{pager}/{page}", name="rolesAdmin",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *     })
     * @Template("AppBundle:admin:roles.html.twig")
     *
     * @return Response
     */
    public function roleAction($page = 1)
    {
        $roles = $this->getDoctrine()
            ->getRepository('AppBundle:Role')
            ->findAll();

        return [
            'roles' => $roles,
        ];
    }
}
