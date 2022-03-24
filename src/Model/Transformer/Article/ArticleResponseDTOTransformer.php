<?php

namespace App\Model\Transformer\Article;

use App\Entity\Article;
use App\Model\Article\ArticleDTO;
use App\Model\Transformer\ResponseDTOTransformerInterface;
use App\Model\Transformer\User\UserSimpleResponseDTOTransformer;

class ArticleResponseDTOTransformer implements ResponseDTOTransformerInterface
{

    /**
     * @param Article $article
     * @return ArticleDTO
     */
    public static function transformFromObject($article): ArticleDTO
    {
        $dto = new ArticleDTO();
        $dto->setId($article->getId());
        $dto->setTitle($article->getTitle());
        $dto->setDescription($article->getDescription());
        $dto->setContent($article->getContent());
        $dto->setCreatedAt($article->getCreatedAt());
        $dto->setUpdateAt($article->getUpdatedAt());
       $dto->setAuthor(UserSimpleResponseDTOTransformer::transformFromObject($article->getAuthor()));
        return $dto;

    }
}