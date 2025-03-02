<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use function dd;

class QuestionRepository
{
    public function getCurrentQuestionForQuiz(Quiz $quiz): ?Question
    {
        return $this->getAllQuestionsForQuiz($quiz)[$quiz->getCurrentQuestion()] ?? null;
    }

    public function getAllQuestionsForQuiz(Quiz $quiz): array
    {
        $raw = Yaml::parseFile($quiz->getQuestionSet())['questions'] ?? [];

        $questions = [];
        foreach ($raw as $question) {
            $answers = [];
            foreach ($question['answers'] as $answer) {
                $answers[] = new Answer($answer['content'], $answer['correct']);
            }

            $questions[] = new Question($question['question'], $answers);
        }

        return $questions;
    }
}
