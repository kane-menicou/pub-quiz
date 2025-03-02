<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use Psr\Clock\ClockInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use function dd;

#[AsLiveComponent]
final class BigScreen
{
    use DefaultActionTrait;

    #[LiveProp]
    public Quiz $quiz;

    public function __construct(private readonly ClockInterface $clock, private readonly QuestionRepository $questionRepository)
    {
    }

    public function getQuestion(): Question
    {
        return $this->questionRepository->getCurrentQuestionForQuiz($this->quiz);
    }
}
