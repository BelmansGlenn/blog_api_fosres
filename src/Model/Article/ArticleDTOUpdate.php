<?php

namespace App\Model\Article;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleDTOUpdate
{
    #[Length(min: 2, max: 100)]
    #[NotBlank]
    private string $title;

    #[Length(min: 5, max: 300, maxMessage: "Use maximum 300 characters")]
    #[NotBlank]
    private string $description;

    #[Length(min: 20, minMessage: "Use at minimum 20 characters")]
    #[NotBlank]
    private string $content;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }



}