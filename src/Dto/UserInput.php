<?php
namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Groups;

class UserInput
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:write'])]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 12, max: 128)]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/",
        message: "Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial."
    )]
    #[Assert\NotCompromisedPassword]
    #[Groups(['user:write'])]
    public ?string $plainPassword = null;

    #[Assert\Length(max: 64)]
    #[Groups(['user:write'])]
    public ?string $username = null;
}
