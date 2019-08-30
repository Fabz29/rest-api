<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route("/articles")
 * Class ArticleController
 * @package App\Controller
 */
class ArticleController extends FOSRestController
{
    /**
     * @Rest\Get()
     * @Rest\QueryParam(name = "order", requirements="asc|desc", default="asc", description="Sort order (asc or desc, default asc)")
     * @Rest\RequestParam(name = "search", requirements="[a-zA-Z0-9]", default=null, nullable=true, description="Search query to look for articles")
     * @param string $order
     * @param string|null $search
     * @param ArticleRepository $articleRepository
     * @return View
     */
    public function list(string $order, ?string $search, ArticleRepository $articleRepository): View
    {
        $articles = $articleRepository->findAll();

        return View::create($articles, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/{id}")
     * @param Article $article
     * @return View
     */
    public function show(Article $article): View
    {
        return View::create($article, Response::HTTP_OK);
    }

    /**
     * @Rest\Post()
     * @ParamConverter("article", converter="fos_rest.request_body")
     * @param Article $article
     * @return View
     */
    public function create(Article $article): View
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return View::create($article, Response::HTTP_CREATED);
    }

    /**
    /**
     * @Rest\Post("/form")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return View
     */
    public function createFromForm(Request $request, SerializerInterface $serializer): View
    {
        $data = $serializer->deserialize($request->getContent(), 'array', 'json');
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return View::create($article, Response::HTTP_CREATED);
    }
}
