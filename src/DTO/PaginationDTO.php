<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Positive;

readonly class PaginationDTO
{
    public function __construct(
        #[Positive]
        public ?int $page = 1
    ){}
}