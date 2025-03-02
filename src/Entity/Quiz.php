<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\QuizRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    public const int FRIENDLY_ID_LENGTH = 6;

    public const int SECONDS_PER_QUESTION = 20;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: self::FRIENDLY_ID_LENGTH)]
    private ?string $friendlyId = null;

    #[ORM\Column(length: 255)]
    private ?string $questionSet = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\OneToMany(targetEntity: Participant::class, mappedBy: 'quiz', orphanRemoval: true)]
    private Collection $participants;

    #[ORM\Column]
    private QuizState $state;

    #[ORM\Column(type: 'integer')]
    private int $currentQuestion = -1;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $lastQuestionStart;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->state = QuizState::Lobby;
        $this->lastQuestionStart = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFriendlyId(): ?string
    {
        return $this->friendlyId;
    }

    public function setFriendlyId(string $friendlyId): static
    {
        $this->friendlyId = $friendlyId;

        return $this;
    }

    public function getQuestionSet(): ?string
    {
        return $this->questionSet;
    }

    public function setQuestionSet(string $questionSet): static
    {
        $this->questionSet = $questionSet;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setQuiz($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getQuiz() === $this) {
                $participant->setQuiz(null);
            }
        }

        return $this;
    }

    public function getState(): QuizState
    {
        return $this->state;
    }

    public function setState(QuizState $state): void
    {
        $this->state = $state;
    }

    public function nextQuestion(): void
    {
        if ($this->state === QuizState::Lobby) {
            $this->state = QuizState::Questions;
        }

        $this->currentQuestion++;
        $this->lastQuestionStart = new DateTimeImmutable();
    }

    public function getCurrentQuestion(): int
    {
        return $this->currentQuestion;
    }

    public function getLastQuestionStart(): DateTimeImmutable
    {
        return $this->lastQuestionStart;
    }
}
