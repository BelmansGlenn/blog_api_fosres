<?php

namespace App\Model\Answer;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerDTOUpdate
{
    #[Length(min: 5, max: 300, maxMessage: "Use maximum 300 characters")]
    #[NotBlank]
    private string $comment;

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }



}