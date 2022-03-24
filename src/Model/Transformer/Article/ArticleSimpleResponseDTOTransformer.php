<?php

namespace App\Model\Transformer\Article;

use App\Entity\Article;
use App\Model\Article\ArticleDTOSimple;
use App\Model\Transformer\ResponseDTOTransformerInterface;

class ArticleSimpleResponseDTOTransformer implements ResponseDTOTransformerInterface
{

    public static function transformFromObjects(iterable $objects): array
    {
        $dto = [];

        foreach ($objects as $object)
        {
            $dto[] = self::transformFromObject($object);

        }

        return $dto;
    }

    /**
     * @param Article $article
     * @return ArticleDTOSimple
     */
    public static function transformFromObject($article): ArticleDTOSimple
    {
        $dto = new ArticleDTOSimple();
        $dto->setId($article->getId());
        $dto->setTitle($article->getTitle());
        $dto->setDescription($article->getDescription());
        $dto->setContent($article->getContent());
        $dto->setCreatedAt($article->getCreatedAt());
        $dto->setUpdateAt($article->getUpdatedAt());
        return $dto;
    }
}