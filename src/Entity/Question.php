<?php

declare(strict_types=1);

namespace App\Entity;

readonly class Question
{
    public function __construct(
        public string $question,
        /** @var list<Answer> */
        public array $answers,
    ) {
    }
}
