<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 */
class ArticleController extends Controller
{
    /**
     * @param Request $request
     * @param $page
     * @Method({"GET", "POST"})
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
    public function roleAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository("AppBundle:Article")
            ->getArticlesWithCountComment($page);

        return [
            'articles'  => $articles,
        ];
    }

    /**
     * @param $id
     * @param $action
     * @param Request $request
     * @Route("/article/{action}/{id}", name="articleEdit",
     *     defaults={"id": 0},
     *     requirements={
     *      "action": "new|edit",
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/form:article.html.twig")
     *
     * @return Response
     */
    public function editArticleAction($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($action == "edit") {
            $article = $em->getRepository('AppBundle:Article')
                ->find($id);
            $title = 'Edit article id: '.$id;
        }
        else {
            $article = new Article();
            $title = 'Create new article';
        }

        $form = $this->createForm(ArticleType::class, $article, [
            'em'        => $em,
            'action'    => $this->generateUrl('articleEdit', ['action' => $action, 'id' => $id]),
            'method'    => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($article);
                $em->flush();

                return $this->redirectToRoute('articlesAdmin');
            }
        }

        return [
            'title' => $title,
            'form'  => $form->createView(),
        ];
    }

    /**
     * @param $id
     * @param Request $request
     * @Route("/article/delete/{id}", name="articleDelete",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/form:delete.html.twig")
     *
     * @return Response
     */
    public function deleteArticleAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')
            ->find($id);
        $message = 'You want to delete article "' . $article->getTitle() . '" (id: ' . $id . '). ';
        $message .= 'Related records will be deleted: comments (count: ' . count($article->getComments()) . '). ';
        $message .= 'Are you sure, you want to continue?';


        $form = $this->createFormBuilder($article)
            ->setAction($this->generateUrl('articleDelete', ['id' => $id]))
            ->setMethod('POST')
            ->add('delete', SubmitType::class, array(
                    'label'     => 'Continue',
                    'attr'      => [
                        'class' => 'btn btn-default'
                    ],
                )
            )
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->remove($article);
                $em->flush();

                return $this->redirectToRoute('articlesAdmin');
            }
        }

        return [
            'message' => $message,
            'form'  => $form->createView(),
        ];
    }

}
