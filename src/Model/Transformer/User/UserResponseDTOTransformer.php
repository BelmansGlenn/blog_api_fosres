<?php

namespace App\Model\Transformer\User;

use App\Entity\User;
use App\Model\Article\ArticleDTO;
use App\Model\Transformer\Article\ArticleSimpleResponseDTOTransformer;
use App\Model\Transformer\ResponseDTOTransformerInterface;
use App\Model\User\UserDTO;

class UserResponseDTOTransformer implements ResponseDTOTransformerInterface
{



    /**
     * @param User $user
     * @return UserDTO
     */
    public static function transformFromObject($user): UserDTO
    {
        $dto = new UserDTO();
        $dto->setId($user->getId());
        $dto->setEmail($user->getEmail());
        $dto->setFirstname($user->getFirstname());
        $dto->setLastname($user->getLastname());
        $dto->setArticles(ArticleSimpleResponseDTOTransformer::transformFromObjects($user->getArticles()->toArray()));

        return $dto;
    }
}