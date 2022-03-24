<?php

namespace App\Model\User;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserDTOUpdate
{
    #[Regex(pattern: '/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%\^\!\?\-\+\.\,\<\>\;\:\=\*])(?!.*(.)\1{2}).*[a-z]/m')]
    #[NotBlank]
    private string $plainPassword;

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