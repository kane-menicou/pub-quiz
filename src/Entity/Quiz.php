<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\QuizRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function max;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    public const int FRIENDLY_ID_LENGTH = 6;

    public const int SECONDS_PER_QUESTION = 120;

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

    #[ORM\Column(type: 'integer')]
    private int $secondsPerQuestion = 0;

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

    public function complete(): void
    {
        $this->state = QuizState::Complete;
    }

    public function getCurrentQuestion(): int
    {
        return $this->currentQuestion;
    }

    public function getLastQuestionStart(): DateTimeImmutable
    {
        return $this->lastQuestionStart;
    }

    public function getSecondsRemaining(): int
    {
        $diff = (new DateTimeImmutable())->diff($this->lastQuestionStart);

        $secondsRemaining = Quiz::SECONDS_PER_QUESTION - ($diff->s + ($diff->i * 60));

        return max($secondsRemaining, 0);
    }

    public function getCountAnswered(): int
    {
        $countAnswered = 0;
        foreach ($this->participants as $participant) {
            if ($participant->hasAnsweredQuestion($this->currentQuestion)) {
                $countAnswered++;
            }
        }

        return $countAnswered;
    }

    public function isCurrentQuestionFinished(): bool
    {
        return $this->getSecondsRemaining() === 0 || $this->getCountAnswered() === $this->participants->count();
    }

    public function isInLobby(): bool
    {
        return $this->state === QuizState::Lobby;
    }

    public function isAnsweringQuestions(): bool
    {
        return $this->state === QuizState::Questions;
    }

    public function getSecondsPerQuestion(): int
    {
        return $this->secondsPerQuestion;
    }

    public function setSecondsPerQuestion(int $secondsPerQuestion): void
    {
        $this->secondsPerQuestion = $secondsPerQuestion;
    }

    public function isComplete(): bool
    {
        return $this->state === QuizState::Complete;
    }
}
