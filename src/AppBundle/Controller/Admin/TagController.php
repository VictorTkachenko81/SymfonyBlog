<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 */
class TagController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/tags", name="tagsAdmin")
     * @Template("AppBundle:admin:tags.html.twig")
     *
     * @return Response
     */
    public function roleAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('AppBundle:Tag')
            ->findAll();

        return [
            'tags'  => $tags,
        ];
    }

    /**
     * @param Request $request
     * @Route("/tag/new", name="tagNew")
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/form:tag.html.twig")
     *
     * @return Response
     */
    public function editRoleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tag = new Tag();
        $title = 'Create new tag';

        $form = $this->createFormBuilder($tag)
            ->setAction($this->generateUrl('tagNew'))
            ->setMethod('POST')
            ->add('name', TextType::class, array(
                    'attr' => array('placeholder' => '* Tag name'),
                )
            )
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($tag);
                $em->flush();

                return $this->redirectToRoute('tagsAdmin');
            }
        }

        return [
            'title' => $title,
            'form'  => $form->createView(),
        ];
    }
}
