<?php

namespace App\Twig\Components;

use App\Entity\Quiz;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PostHydrate;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use function max;

#[AsLiveComponent]
final class Question
{
    use DefaultActionTrait;

    #[LiveProp]
    public Quiz $quiz;

    public function __construct(private readonly ClockInterface $clock)
    {
    }

    public function getQuestion(): array
    {
        return Yaml::parseFile($this->quiz->getQuestionSet())['questions'][$this->quiz->getCurrentQuestion()] ?? throw new NotFoundHttpException("No Question {$this->quiz->getCurrentQuestion()} for Quiz {$this->quiz->getFriendlyId()}");
    }

    public function getSecondsRemaining(): int
    {
        $diff = $this->clock->now()->diff($this->quiz->getLastQuestionStart());

        $secondsRemaining = Quiz::SECONDS_PER_QUESTION - ($diff->s + ($diff->i * 60));

        return max($secondsRemaining, 0);
    }

    public function getCountAnswered(): int
    {
        $countAnswered = 0;
        foreach ($this->quiz->getParticipants() as $participant) {
            if ($participant->hasAnsweredQuestion($this->quiz->getCurrentQuestion())) {
                $countAnswered++;
            }
        }

        return $countAnswered;
    }
}
