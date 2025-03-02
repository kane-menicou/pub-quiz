<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Participant;
use App\Entity\Question;
use App\Repository\ParticipantRepository;
use App\Repository\QuestionRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use function dd;

#[AsLiveComponent]
final class ParticipantView
{
    use DefaultActionTrait;

    private const int DEFAULT_POLLING_DELAY = 500;

    #[LiveProp]
    public Participant $participant;

    #[LiveProp(writable: true, onUpdated: 'addAnswer')]
    public ?string $lastAnswer = null;

    public function __construct(private readonly QuestionRepository $questionRepository, private readonly ParticipantRepository $participantRepository)
    {
    }

    public function getPendingCurrentQuestion(): ?Question
    {
        $quiz = $this->participant->getQuiz();
        if ($quiz->isCurrentQuestionFinished() || !$quiz->isAnsweringQuestions() || $this->participant->hasAnsweredQuestion($quiz->getCurrentQuestion())) {
            return null;
        }

        return $this->questionRepository->getCurrentQuestionForQuiz($quiz);
    }

    public function getPollingDelayMs(): int
    {
        $quiz = $this->participant->getQuiz();
        if (!$quiz->isCurrentQuestionFinished() && $quiz->isAnsweringQuestions()) {
            return $quiz->getSecondsRemaining() * 1_000;
        }

        return self::DEFAULT_POLLING_DELAY;
    }

    public function addAnswer(): void
    {
        $quiz = $this->participant->getQuiz();
        foreach ($this->questionRepository->getCurrentQuestionForQuiz($quiz)->answers as $index => $answer) {
            if ($answer->content === $this->lastAnswer) {
                $this->lastAnswer = null;

                $this->participant->addAnswer($quiz->getCurrentQuestion(), $index);
            }
        }

        $this->participantRepository->save($this->participant);
    }
}
