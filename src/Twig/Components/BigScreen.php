<?php

namespace App\Twig\Components;

use App\Entity\Quiz;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BigScreen
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
}
