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
        $em = $this->getDoctrine()->getManager();
        $countArticles = $em->getRepository("AppBundle:Article")
            ->getCountArticles();

        $countComments = $em->getRepository("AppBundle:Comment")
            ->getCountComments();

        $countUsers = $em->getRepository("AppBundle:User")
            ->getCountUsers();

        return [
            'countArticles' => $countArticles['countArticles'],
            'countComments' => $countComments['countComments'],
            'countUsers'    => $countUsers['countUsers'],
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
