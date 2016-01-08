<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentType;
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

    //ToDo: AjaxSubmit
    /**
     * @param Request $request
     * @param $slug
     * @Route("/newCommentFor/{slug}", name="commentForm")
     * @Template("AppBundle:frontend:commentForm.html.twig")
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

        //ToDo: createdAt must auto fill in base??
        $comment->setCreatedAt(new \DateTime('now'));

        $form = $this->createForm(CommentType::class, $comment, [
            'em' => $em,
            'action' => $this->generateUrl('commentForm', ['slug' => $slug]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Submit Comment',
                    'attr' => array('class' => "btn btn-primary")
                ));

        //ToDo: fix check isUserAnonymous
        if ($user = 'anonymous') {
            $form
                ->add('user', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'choice_label' => 'name',
                    'placeholder' => '* Choose user',
                ))
                ->add('name', TextType::class, array(
                        'attr' => array('placeholder' => '* Name')
                    )
                )
                ->add('email', EmailType::class, array(
                        'attr' => array('placeholder' => '* Email')
                    )
                );
        }

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('showArticle', ['slug' => $slug]);
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
