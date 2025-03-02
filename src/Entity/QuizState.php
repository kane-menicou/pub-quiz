<?php

declare(strict_types=1);

namespace App\Entity;

enum QuizState: string
{
    case Lobby = 'lobby';
    case Questions = 'questions';
    case Complete = 'complete';
}
