<?php

namespace AppBundle\Controller\Blog;

use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentType;
use AppBundle\Form\Type\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class BlogController extends Controller
{

    /**
     * @param $page
     * @Method("GET")
     * @Route("/{pager}/{page}", name="homepage",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *     })
     * @Template("AppBundle:blog:blogPosts.html.twig")
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
     * @Template("AppBundle:blog:blogSingle.html.twig")
     *
     * @return Response
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository("AppBundle:Article")
            ->getArticleWithDep($slug);

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
     *          "page": "[1-9]\d*",
     *     })
     * @Template("AppBundle:blog:blogPosts.html.twig")
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
     * @param Request $request
     * @Method("POST")
     * @Route("/search", name="searchArticles")
     * @Template("AppBundle:blog:blogPosts.html.twig")
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $articles = $em->getRepository("AppBundle:Article")
                ->getArticlesSorted('search', $data['param'], 1, 100);

            return [
                'articles' => $articles,
            ];
        }
        else {
            return [];
        }
    }

    /**
     * @param Request $request
     * @param $slug
     * @Route("/newCommentFor/{slug}", name="commentForm")
     * @Template("AppBundle:blog:commentForm.html.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newCommentAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository("AppBundle:Article")
            ->findOneBySlug($slug);
        $comment = new Comment();
        $comment->setArticle($article);

        $form = $this->createForm(CommentType::class, $comment, [
            'em' => $em,
            'action' => $this->generateUrl('commentForm', ['slug' => $slug]),
            'method' => Request::METHOD_POST,
        ]);

        //ToDo: check isUserAnonymous
        if ($user = 'anonymous') {
            $form
                ->add('user', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'choice_label' => 'name',
                    'placeholder' => '* Choose user (remove after security)',
                ))
                ->add('name', TextType::class, array(
                        'attr' => array('placeholder' => '* Name (anonymous)')
                    )
                )
                ->add('email', EmailType::class, array(
                        'attr' => array('placeholder' => '* Email (anonymous)')
                    )
                );
        }

        $form
            ->add('save', SubmitType::class, array(
                'label' => 'Submit Comment',
                'attr' => array('class' => "btn btn-primary")
            ));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('success');
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param $slug
     * @param $page
     * @Route("/comments/{slug}/{pager}/{page}", name="articleComments",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *     })
     * @Template("AppBundle:blog:comments.html.twig")
     *
     * @return Response
     */
    public function showArticleCommentsAction($slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository("AppBundle:Comment")
            ->getArticleComment($slug, $page, 5);

        return [
            'comments' => $comments,
        ];
    }

    /**
     * @Route("/success", name="success")
     * @Template("AppBundle:blog:success.html.twig")
     *
     * @return Response
     */
    public function successAction()
    {
        return [];
    }

    /**
     * @Route("/search_form_render", name="searchFormRender")
     * @Template("AppBundle:blog:widgetSearchForm.html.twig")
     *
     * @return Response
     */
    public function createSearchFormAction()
    {
        $form = $this->createForm(SearchType::class);

        return [
            'form' => $form->createView(),
        ];
    }
}
