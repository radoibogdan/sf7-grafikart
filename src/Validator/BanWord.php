<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BanWord extends Constraint
{
    public function __construct(
        public string $message = 'Le texte contient un mot interdit "{{ bannedWord }}".',
        public array  $bannedWords = ['spam', 'viagra'],
        public ?array $groups = null,
        public mixed  $payload = null,
    ){
        parent::__construct(null, $groups, $payload);
    }
}
