<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Quiz;
use App\Repository\ParticipantRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ParticipantsList
{
    use DefaultActionTrait;

    #[LiveProp]
    public Quiz $quiz;

    public function __construct(private ParticipantRepository $repository)
    {

    }

    public function getParticipants(): array
    {
        return $this->repository->findByQuiz($this->quiz);
    }
}
