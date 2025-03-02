<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use function count;
use function dd;

#[AsLiveComponent]
final class BigScreen extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public Quiz $quiz;

    public function __construct(private readonly QuestionRepository $questionRepository, private readonly QuizRepository $quizRepository)
    {
    }

    public function getQuestion(): Question
    {
        return $this->questionRepository->getCurrentQuestionForQuiz($this->quiz);
    }

    public function getAnswerBreakdownToCurrentQuestion(): array
    {
        $question = $this->questionRepository->getCurrentQuestionForQuiz($this->quiz);
        if ($question === null) {
            return [];
        }

        $answerBreakdown = [];
        foreach ($question->answers as $answerIndex => $answer) {
            $chosenBy = [];
            foreach ($this->quiz->getParticipants() as $participant) {
                if ($participant->getAnswerToQuestion($this->quiz->getCurrentQuestion()) === $answerIndex) {
                    $chosenBy[] = $participant;
                }
            }

            $answerBreakdown[] = [
                'answer' => $answer,
                'chosenBy' => $chosenBy
            ];
        }

        $notAnswered = [];
        foreach ($this->quiz->getParticipants() as $participant) {
            if ($participant->getAnswerToQuestion($this->quiz->getCurrentQuestion()) === null) {
                $notAnswered[] = $participant;
            }
        }


        $answerBreakdown[] = [
            'answer' => null,
            'chosenBy' => $notAnswered,
        ];

        return $answerBreakdown;
    }

    public function isLastQuestion(): bool
    {
        $nextQuestion = $this->quiz->getCurrentQuestion() + 1;
        $numberOfQuestions = count($this->questionRepository->getAllQuestionsForQuiz($this->quiz));

        return $nextQuestion >= $numberOfQuestions;
    }

    #[LiveAction]
    public function nextPage(): ?Response
    {
        if ($this->isLastQuestion()) {
            $this->quiz->complete();
        } else {
            $this->quiz->nextQuestion();
        }

        $this->quizRepository->save($this->quiz);

        if ($this->quiz->isComplete()) {
            return $this->redirectToRoute('app_quiz_view', ['quiz' => $this->quiz->getFriendlyId()]);
        }

        return null;
    }
}
