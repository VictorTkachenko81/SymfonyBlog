<?php

namespace AppBundle\Controller\Blog;

use AppBundle\Form\Type\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class BlogController extends Controller
{

    /**
     * @param $page
     * @Method("GET")
     * @Route("/{_locale}/{pager}/{page}", name="homepage",
     *     defaults={"pager": "page", "page": 1, "_locale": "%locale%"},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *          "_locale": "%available.locales%"
     *     })
     * @Template("AppBundle:blog:blogPosts.html.twig")
     *
     * @return Response
     */
    public function indexAction($page = 1)
    {
        $blogHandler = $this->container->get('app.blog_handler');
        $articles = $blogHandler->getArticles($page);

        return [
            'articles' => $articles,
        ];
    }

    /**
     * @param $slug
     * @Method("GET")
     * @Route("/{_locale}/article/{slug}", name="showArticle",
     *     defaults={"_locale": "%locale%"},
     *     requirements={"_locale": "%available.locales%"})
     * @Template("AppBundle:blog:blogSingle.html.twig")
     *
     * @return Response
     */
    public function showAction($slug)
    {
        $blogHandler = $this->container->get('app.blog_handler');
        $article = $blogHandler->getArticle($slug);

        return [
            'article' => $article,
        ];
    }

    /**
     * @param $sortBy
     * @param $param
     * @param $page
     * @Method("GET")
     * @Route("/{_locale}/{sortBy}/{param}/{pager}/{page}", name="sortArticles",
     *     defaults={"pager": "page", "page": 1, "_locale": "%locale%"},
     *     requirements={
     *          "sortBy": "category|tag|author|date",
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *          "_locale": "%available.locales%",
     *     })
     * @Template("AppBundle:blog:blogPosts.html.twig")
     *
     * @return Response
     */
    public function sortAction($sortBy, $param, $page = 1)
    {
        $blogHandler = $this->container->get('app.blog_handler');
        if ($sortBy == 'date') $blogHandler->validate($param, new Assert\Date);
        $articles = $blogHandler->getArticlesSorted($sortBy, $param, $page);

        return [
            'articles' => $articles,
        ];
    }

    /**
     * @param Request $request
     * @Method("POST")
     * @Route("/{_locale}/search", name="searchArticles",
     *     defaults={"_locale": "%locale%"},
     *     requirements={"_locale": "%available.locales%"})
     * @Template("AppBundle:blog:blogPosts.html.twig")
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $blogHandler = $this->container->get('app.blog_handler');

        return $blogHandler->searchArticles($request);
    }

    /**
     * @param Request $request
     * @param $slug
     * @param $id
     * @Route("/{_locale}/commentFor/{slug}/{id}", name="commentForm",
     *     defaults={"id": 0, "_locale": "%locale%"},
     *     requirements={
     *          "id": "\d+",
     *          "_locale": "%available.locales%",
     *     })
     * @Template("AppBundle:blog:commentForm.html.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newCommentAction(Request $request, $slug, $id = null)
    {
        $blogHandler = $this->container->get('app.blog_handler');

        return $blogHandler->addComment($request, $slug, $id);
    }

    /**
     * @param $slug
     * @param $page
     * @Route("/{_locale}/comments/{slug}/{pager}/{page}", name="articleComments",
     *     defaults={"pager": "page", "page": 1, "_locale": "%locale%"},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*",
     *          "_locale": "%available.locales%",
     *     })
     * @Template("AppBundle:blog:comments.html.twig")
     *
     * @return Response
     */
    public function showArticleCommentsAction($slug, $page = 1)
    {
        $blogHandler = $this->container->get('app.blog_handler');
        $comments = $blogHandler->getComments($slug, $page);

        return [
            'comments' => $comments,
        ];
    }

    /**
     * @Route("/{_locale}/success", name="success",
     *     defaults={"_locale": "%locale%"},
     *     requirements={"_locale": "%available.locales%"})
     * @Template("AppBundle:blog:success.html.twig")
     *
     * @return Response
     */
    public function successAction()
    {
        return [];
    }

    /**
     * @Route("/{_locale}/search_form_render", name="searchFormRender",
     *     defaults={"_locale": "%locale%"},
     *     requirements={"_locale": "%available.locales%"})
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

    /**
     * @param $id
     * @param Request $request
     * @Route("/{_locale}/deleteComment/{id}", name="deleteFormRender",
     *     defaults={"_locale": "%locale%"},
     *     requirements={
     *          "id": "\d+",
     *          "_locale": "%available.locales%"})
     * @Template("AppBundle:blog:widgetDeleteForm.html.twig")
     *
     * @return Response
     */
    public function createDeleteFormAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')
            ->find($id);

        $form = $this->createFormBuilder($comment)
            ->setAction($this->generateUrl('deleteFormRender', ['id' => $id]))
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
                $slug = $comment->getArticle()->getSlug();
                $em->remove($comment);
                $em->flush();

                return $this->redirectToRoute('showArticle', ['slug' => $slug]);
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
