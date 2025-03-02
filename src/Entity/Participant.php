<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: 'json')]
    private array $answers = [];

    public function addAnswer(int $question, int $answer): void
    {
        $this->answers[$question] = $answer;
    }

    public function getAnswerToQuestion(int $question): ?int
    {
        return $this->answers[$question] ?? null;
    }

    public function hasAnsweredQuestion(int $question): bool
    {
        return array_key_exists($question, $this->answers);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuiz(): ?quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }
}
