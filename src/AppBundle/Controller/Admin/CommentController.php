<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 */
class CommentController extends Controller
{
    /**
     * @param Request $request
     * @param $page
     * @Method({"GET", "POST"})
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
    public function roleAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository("AppBundle:Comment")
            ->getRecentComments($page, 10);

        $formData = $comments->getQuery()->getResult();

        $form = $this->createFormBuilder($formData)
            ->setAction($this->generateUrl('commentsAdmin'))
            ->setMethod('POST')
            ->add('comments', ChoiceType::class, array(
                    'choices'           => $formData,
                    'choices_as_values' => true,
                    'expanded'          => true,
                    'multiple'          => true,
                    'choice_value'      => 'id',
                    'label'             => false,
                    'choice_label'      => 'id',
                )
            )
            ->add('delete', SubmitType::class, array(
                    'label'     => 'Remove',
                    'attr'      => [
                        'class' => 'btn btn-xs btn-danger'
                    ],
                )
            )
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                foreach ($data['comments'] as $comment) {
                    $em->remove($comment);
                }

                try {
                    $em->flush();
                } catch (\Exception $e) {
                    return $this->render(
                        'AppBundle:admin:failure.html.twig',
                        array(
                            'message' => 'Deleting record is failed. Because record has relation to other records or another reasons.',
                        )
                    );
                }

                return $this->redirectToRoute('commentsAdmin');
            }
        }

        return [
            'comments'  => $comments,
            'delete' => $form->createView(),
        ];
    }

    /**
     * @param $id
     * @param $action
     * @param Request $request
     * @Route("/comment/{action}/{id}", name="commentEdit",
     *     defaults={"id": 0},
     *     requirements={
     *      "action": "edit",
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/form:comment.html.twig")
     *
     * @return Response
     */
    public function editCommentAction($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')
            ->find($id);
        $title = 'Edit comment id: '.$id. ' for article "' . $comment->getArticle()->getTitle() . '"';

        $form = $this->createForm(CommentAdminType::class, $comment, [
            'em' => $em,
            'action' => $this->generateUrl('commentEdit', ['action' => $action, 'id' => $id]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('commentsAdmin');
            }
        }

        return [
            'title' => $title,
            'form'  => $form->createView(),
        ];
    }

}
