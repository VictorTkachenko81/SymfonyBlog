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
     * @Route("/", name="homepage")
     * @Template("AppBundle:frontend:index.html.twig")
     *
     * @return Response
     */
    public function indexAction()
    {
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->findAll();

        return [
            'articles' => $articles,
        ];
    }
}
