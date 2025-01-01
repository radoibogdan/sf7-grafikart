<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BanWordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var BanWord $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $value = strtolower($value);
        foreach($constraint->bannedWords as $bannedWord) {
            if (str_contains($value, $bannedWord)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ bannedWord }}', $bannedWord)
                    ->addViolation();
            }
        }
    }
}
