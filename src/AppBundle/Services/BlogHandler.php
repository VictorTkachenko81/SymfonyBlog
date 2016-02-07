<?php
namespace AppBundle\Services;

use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BlogHandler
{

    protected $doctrine;
    protected $formFactory;
    protected $router;
    protected $validator;

    public function __construct(RegistryInterface $doctrine,
                                FormFactoryInterface $formFactory,
                                RouterInterface $router,
                                ValidatorInterface $validator
    )
    {
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->validator = $validator;
    }

    public function getArticles($page)
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository("AppBundle:Article")
            ->getArticlesWithCountComment($page);
    }

    public function getArticle($slug)
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository("AppBundle:Article")
            ->getArticleWithDep($slug);
    }

    public function getArticlesSorted($sortBy, $param, $page)
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository("AppBundle:Article")
            ->getArticlesSorted($sortBy, $param, $page);
    }

    public function searchArticles(Request $request)
    {
        $form = $this->formFactory->create(SearchType::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $em = $this->doctrine->getManager();
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

    public function getComments($slug, $page)
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository("AppBundle:Comment")
            ->getArticleComment($slug, $page, 5);
    }

    public function addComment(Request $request, $slug)
    {
        $em = $this->doctrine->getManager();
        $article = $em->getRepository("AppBundle:Article")
            ->findOneBySlug($slug);
        $comment = new Comment();
        $comment->setArticle($article);

        $form = $this->formFactory->create(CommentType::class, $comment, [
            'em' => $em,
            'action' => $this->router->generate('commentForm', ['slug' => $slug]),
            'method' => Request::METHOD_POST,
        ]);

        //ToDo: check isUserAnonymous
        if ($user = 'anonymous') {
            $form
                ->add('user', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'choice_label' => 'username',
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

                return new RedirectResponse($this->router->generate('success'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    public function validate($param, $constraint)
    {
        $constraint->message = 'Invalid date';

        $errorList = $this->validator->validate($param, $constraint);

        if (0 !== count($errorList)) {
            throw new NotFoundHttpException(
                $errorMessage = $errorList[0]->getMessage()
            );
        }
    }
}