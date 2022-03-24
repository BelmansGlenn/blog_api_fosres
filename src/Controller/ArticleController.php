<?php

namespace App\Controller;

use App\Entity\Article;
use App\Model\Article\ArticleDTOUpdate;
use App\Model\Transformer\Article\ArticleResponseDTOTransformer;
use App\Model\Transformer\Article\ArticleSimpleResponseDTOTransformer;
use App\Repository\ArticleRepository;
use App\Services\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;

class ArticleController extends AbstractFOSRestController
{


    #[Post('/api/article', name: 'app_article')]
    #[View(statusCode: 201)]
    #[ParamConverter("article", converter: "fos_rest.request_body")]
    public function index(Article $article, EntityManagerInterface $entityManager, ConstraintViolationList $violations)
    {
        if (count($violations)){
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("\nField %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new BadRequestException($message);
        }
        $article->setAuthor($this->getUser());
        $entityManager->persist($article);
        $entityManager->flush();

        return ArticleSimpleResponseDTOTransformer::transformFromObject($article);

    }

    #[Get(path: "/api/articles", name: "app_article_all")]
    #[QueryParam(name: "keyword",requirements: "[a-zA-Z0-9]", nullable: true, description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "10", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View]
    public function allArticles(ArticleRepository $repository, ParamFetcherInterface $paramFetcher, Request $request)
    {

        return Pagination::paginate($repository, $paramFetcher, $request, fn($it)=> ArticleResponseDTOTransformer::transformFromObject($it));
    }


    #[Get(path: "/api/article/{id}", name: "app_article_id", requirements: ["id" => "\d+"])]
    #[View]
    public function getArticleById($id, ArticleRepository $articleRepository){

        $article = $articleRepository->find($id);
        if (!$article){
            throw new NotFoundHttpException();
        }

        return ArticleResponseDTOTransformer::transformFromObject($article);
    }


    #[Patch(path: "/api/article/{id}", name: "app_article_update", requirements: ['id' => '\d+'])]
    #[View(statusCode: 200)]
    #[ParamConverter("articleDTOUpdate", converter: "fos_rest.request_body")]
    public function updateArticleById($id, EntityManagerInterface $entityManager, ArticleDTOUpdate $articleDTOUpdate, ConstraintViolationList $violations){

        if (count($violations)){
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("\nField %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new BadRequestException($message);
        }

        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article){
            throw new NotFoundHttpException();
        }

        $article->setTitle($articleDTOUpdate->getTitle());
        $article->setDescription($articleDTOUpdate->getDescription());
        $article->setContent($articleDTOUpdate->getContent());
        $entityManager->flush();

        return ArticleSimpleResponseDTOTransformer::transformFromObject($article);

    }
    #[Delete(path: "/api/article/{id}", name: "app_article_delete", requirements: ['id' => '\d+'])]
    #[View]
    public function deleteArticleById($id, ArticleRepository $articleRepository){
        $article = $articleRepository->find($id);

         if (!$article){
             throw new NotFoundHttpException();
         }

         $articleRepository->remove($article);
    }

}
