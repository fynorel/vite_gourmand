<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordRequirementsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordRequirements) {
            throw new UnexpectedTypeException($constraint, PasswordRequirements::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        $password = (string) $value;

        if (strlen($password) < 10) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        if (!preg_match('/[a-z]/', $password)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        if (!preg_match('/[0-9]/', $password)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }
    }
}
