<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @var ArticleRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct( ArticleRepository $repository, EntityManagerInterface $em ){
       $this->repository = $repository;
       $this->em = $em;
    }

    /**
     * @Route("/article", name="article.index")
     * @return \Symfony\Component\HttpFoundation\Response
     * @var ArticleRepository[] $articles
     */

    public function index(): Response
    {
        $articles = $this->repository->findAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles'=> $articles
        ]);
    }


    /**
     * @Route("/article/{id}", name="article.show", methods="GET|POST")
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        $article = $this->repository->find($id);

        return $this->render('article/show.html.twig', [

            'controller_name' => 'ArticleController',
            'article'=> $article
        ]);
    }


    /**
     * @Route("/create", name="article.create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
       $article = new Article();
       $form = $this->createForm(ArticleType::class, $article);
       $form->handleRequest($request);

       if( $form->isSubmitted() && $form->isValid() ){
           $this->em->persist($article);
           $this->em->flush();
           return $this->redirectToRoute('article.index');
       }

        return $this->render('article/create.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="article.edit")
     * @param Request $request
     * @return Response
     */
    public function edit(Article $article, Request $request): Response
    {
       $form = $this->createForm(ArticleType::class, $article);
       $form->handleRequest($request);

       if( $form->isSubmitted() && $form->isValid() ){
            $this->em->flush();
           return $this->redirectToRoute('article.show', array(
               'id' => $article->getId(),
           ));
       }

        return $this->render('article/create.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}", name="article.delete", methods="DELETE")
     * @param Request $request
     * @return Response
     */
    public function delete(Article $article, Request $request): Response
    {
       $this->em->remove($article);
       $this->em->flush();

       return $this->redirectToRoute('article.index');
    }

}