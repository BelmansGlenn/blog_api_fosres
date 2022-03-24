<?php

namespace App\Model\Transformer\Answer;

use App\Entity\Answer;
use App\Model\Answer\AnswerDTOSimple;
use App\Model\Transformer\ResponseDTOTransformerInterface;

class AnswerSimpleResponseDTOTransformer implements ResponseDTOTransformerInterface
{


    /**
     * @param Answer $answer
     * @return AnswerDTOSimple
     */
    public static function transformFromObject($answer): AnswerDTOSimple
    {
        $dto = new AnswerDTOSimple();
        $dto->setId($answer->getId());
        $dto->setComment($answer->getComment());
        $dto->setCreatedAt($answer->getCreatedAt());
        $dto->setUpdateAt($answer->getUpdatedAt());
        return $dto;
    }
}