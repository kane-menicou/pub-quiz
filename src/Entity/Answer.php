<?php

declare(strict_types=1);

namespace App\Entity;

class Answer
{
    public function __construct(
        public string $content,
        public bool   $correct,
    ) {
    }
}
