<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Role;
use AppBundle\Form\Type\RoleType;
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
class RoleController extends Controller
{
    /**
     * @param Request $request
     * @param $page
     * @Method({"GET", "POST"})
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
    public function roleAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('AppBundle:Role')
            ->findAll();

        $form = $this->createFormBuilder($roles)
            ->setAction($this->generateUrl('rolesAdmin'))
            ->setMethod('POST')
            ->add('roles', ChoiceType::class, array(
                    'choices'           => $roles,
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
                foreach ($data['roles'] as $role) {
                    $em->remove($role);
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

                return $this->redirectToRoute('rolesAdmin');
            }
        }

        return [
            'roles'  => $roles,
            'delete' => $form->createView(),
        ];
    }

    /**
     * @param $id
     * @param $action
     * @param Request $request
     * @Route("/role/{action}/{id}", name="roleEdit",
     *     defaults={"id": 0},
     *     requirements={
     *      "action": "new|edit",
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/form:role.html.twig")
     *
     * @return Response
     */
    public function editRoleAction($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($action == "edit") {
            $role = $em->getRepository('AppBundle:Role')
                ->find($id);
            $title = 'Edit role id: '.$id;
        }
        else {
            $role = new Role();
            $title = 'Create new role';
        }


        $form = $this->createForm(RoleType::class, $role, [
            'em' => $em,
            'action' => $this->generateUrl('roleEdit', ['action' => $action, 'id' => $id]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($role);
                $em->flush();

                return $this->redirectToRoute('rolesAdmin');
            }
        }

        return [
            'title' => $title,
            'form'  => $form->createView(),
        ];
    }

}
