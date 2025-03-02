<?php

namespace App\Twig\Components;

use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use function sort;

#[AsTwigComponent]
final class Leaderboard
{
    public Quiz $quiz;

    public function __construct(private QuestionRepository $questionRepository)
    {
    }

    public function getCurrentScores(): array
    {
        $questions = $this->questionRepository->getAllQuestionsForQuiz($this->quiz);
        $participantScores = [];

        $participantsById = [];
        foreach ($this->quiz->getParticipants() as $participant) {
            $participantsById[] = $participant;
        }

        foreach ($this->quiz->getParticipants() as $participant) {
            $participantScores[$participant->getId()] = 0;
        }

        foreach ($questions as $questionIndex => $question) {
            $correctAnswerIndex = null;
            foreach ($question->answers as $index => $answer) {
                if ($answer->correct) {
                    $correctAnswerIndex = $index;

                    break;
                }
            }

            foreach ($this->quiz->getParticipants() as $participant) {
                if ($participant->getAnswerToQuestion($questionIndex) === $correctAnswerIndex) {
                    $participantScores[$participant->getId()] = $participantScores[$participant->getId()] + 1;
                }
            }

            sort($participantScores);

            $result = [];
            foreach ($participantScores as $participantId => $score) {
                $result[] = ['participant' => $participantsById[$participantId], 'score' => $score];
            }

            return $result;
        }
    }
}
