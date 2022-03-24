<?php

namespace App\Model\User;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserDTOCreate
{
    #[Email]
    private string $email;
    #[Length(min: 3, max: 20)]
    #[NotBlank]
    private string $firstname;
    #[Length(min: 3, max: 30)]
    #[NotBlank]
    private string $lastname;

    #[Regex(pattern: '/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%\^\!\?\-\+\.\,\<\>\;\:\=\*])(?!.*(.)\1{2}).*[a-z]/m')]
    #[NotBlank]
    private string $plainPassword;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }














}