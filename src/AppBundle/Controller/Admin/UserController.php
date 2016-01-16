<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 */
class UserController extends Controller
{
    /**
     * @param Request $request
     * @param $page
     * @Method({"GET", "POST"})
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
    public function roleAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')
            ->findAll();

        return [
            'users'  => $users,
        ];
    }

    /**
     * @param $id
     * @param $action
     * @param Request $request
     * @Route("/user/{action}/{id}", name="userEdit",
     *     defaults={"id": 0},
     *     requirements={
     *      "action": "new|edit",
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/form:user.html.twig")
     *
     * @return Response
     */
    public function editUserAction($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($action == "edit") {
            $user = $em->getRepository('AppBundle:User')
                ->find($id);
            $title = 'Edit user id: '.$id;
        }
        else {
            $user = new User();
            $title = 'Create new user';
        }


        $form = $this->createForm(UserType::class, $user, [
            'em' => $em,
            'action' => $this->generateUrl('userEdit', ['action' => $action, 'id' => $id]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('usersAdmin');
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
     * @Route("/user/delete/{id}", name="userDelete",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/form:delete.html.twig")
     *
     * @return Response
     */
    public function deleteUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->find($id);

        $countArticles = count($user->getArticles());
        $countComments = count($user->getComments());

        $message = 'You want to delete user "' . $user->getName() . '" (id: ' . $id . '). ';
        $message .= 'Related records: articles (count: ' . $countArticles . '), ';
        $message .= 'comments (count: ' . $countComments . '). ';

        if ($countArticles == 0 or $countComments == 0) {
            $message .= 'Are you sure, you want to continue?';

            $form = $this->createFormBuilder($user)
                ->setAction($this->generateUrl('userDelete', ['id' => $id]))
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
                    $em->remove($user);
                    $em->flush();

                    return $this->redirectToRoute('usersAdmin');
                }
            }

            $renderedForm = $form->createView();
        }
        else {
            $message .= 'You must to delete related records before.';
            $renderedForm = '';
        }

        return [
            'message' => $message,
            'form'    => $renderedForm,
        ];
    }
}
