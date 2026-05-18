<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[HasNamedArguments]
class PasswordRequirements extends Constraint
{
    public string $message = 'Le mot de passe doit contenir au moins 10 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.';

    public function validatedBy(): string
    {
        return PasswordRequirementsValidator::class;
    }
}
