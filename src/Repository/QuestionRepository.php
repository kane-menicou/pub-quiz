<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Cache\CacheInterface;
use function dd;

class QuestionRepository
{
    public function __construct(private CacheInterface $cache, #[Autowire('%kernel.project_dir%')] private readonly string $projectDir)
    {
    }

    public function getCurrentQuestionForQuiz(Quiz $quiz): ?Question
    {
        return $this->getAllQuestionsForQuiz($quiz)[$quiz->getCurrentQuestion()] ?? null;
    }

    public function getAllQuestionsForQuiz(Quiz $quiz): array
    {
        $questionSet = $quiz->getQuestionSet();
        return $this->cache->get("questionSet_$questionSet", function () use ($questionSet): array {
            $raw = Yaml::parseFile($questionSet)['questions'] ?? [];

            $questions = [];
            foreach ($raw as $question) {
                $answers = [];
                foreach ($question['answers'] as $answer) {
                    $answers[] = new Answer($answer['content'], $answer['correct']);
                }

                $questions[] = new Question($question['question'], $answers);
            }

            return $questions;
        });
    }

    /**
     * @return array<string, string>
     */
    public function listAllQuestionSets(): array
    {
        return $this->cache->get('questionSets', function () {
            $questionSets = (new Finder())->in($this->projectDir)->path('questions')->name(['*.yaml', '*.yml']);

            $names = [];
            foreach ($questionSets as $questionSet) {
                $names[$questionSet->getFilename()] = $questionSet->getRealPath();
            }

            return $names;
        });
    }
}
