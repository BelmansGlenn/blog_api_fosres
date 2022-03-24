<?php

namespace App\Model\Transformer\User;

use App\Entity\User;
use App\Model\Transformer\ResponseDTOTransformerInterface;
use App\Model\User\UserDTOSimple;

class UserSimpleResponseDTOTransformer implements ResponseDTOTransformerInterface
{

    /**
     * @param User $user
     * @return UserDTOSimple
     */
    public static function transformFromObject($user): UserDTOSimple
    {
        $dto = new UserDTOSimple();
        $dto->setId($user->getId());
        $dto->setEmail($user->getEmail());
        $dto->setFirstname($user->getFirstname());
        $dto->setLastname($user->getLastname());

        return $dto;
    }
}