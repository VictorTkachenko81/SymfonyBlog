<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\Type\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterController extends Controller
{
    /**
     * @param Request $request
     *
     * @Method({"GET", "POST"})
     * @Route("/register", name="register")
     * @Template("AppBundle:security:register.html.twig")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $user = new User();
        $user->addRoleObject($this->getRole());
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(RegisterType::class, $user, [
            'em' => $em,
            'action' => $this->generateUrl('register'),
            'method' => Request::METHOD_POST,
        ])
            ->add('submit', SubmitType::class, array(
                'label'     => 'Create Account',
                'attr'      => [
                    'class' => 'btn btn-primary btn-block'
                ]));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $data = $form->getData();
                $plainPassword = $data->getPassword();

                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword(new User(), $plainPassword);
                $user->setPassword($encoded);

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('login_route');
            }
        }

        return [
            'form'  => $form->createView(),
        ];
    }

    protected function getRole()
    {
        $em = $this->getDoctrine()->getManager();
        $role = $em->getRepository("AppBundle:Role")
            ->findOneByName('ROLE_USER');
        if (count($role) == 0) {
            $role = new Role();
            $role->setName('ROLE_USER');
            $em->persist($role);
            $em->flush();
        }

        return $role;
    }
}