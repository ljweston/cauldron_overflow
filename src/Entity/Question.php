<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\QuestionRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    use TimestampableEntity; // trait with our fields and annotations

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $name;

    #[ORM\Column(type: 'string', length: 100, unique: true, name: 'slug')]
    #[Gedmo\Slug(fields: ['name'])]
    private $slug; // slug is automatically set to name (title) with dashes

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private $question;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $askedAt;

    #[ORM\Column(type: 'integer')]
    private $votes = 0;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class, fetch:"EXTRA_LAZY")]
    #[ORM\OrderBy(['createdAt'=> 'DESC'])]
    private $answers;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: QuestionTag::class)]
    private $questionTags;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->questionTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAskedAt(): ?\DateTime
    {
        return $this->askedAt;
    }

    public function setAskedAt(?\DateTime $askedAt): self
    {
        $this->askedAt = $askedAt;

        return $this;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function getVotesString(): string
    {
        $prefix = $this->getVotes() >= 0 ? '+' : '-';

        return sprintf('%s %d', $prefix, abs($this->getVotes()));
    }

    public function setVotes(int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }

    public function upVote(): self
    {
        $this->votes++;

        return $this;
    }

    public function downVote(): self
    {
        $this->votes--;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function getApprovedAnswers(): Collection
    {
        // makes a call to AnswerRep function that returns a criteria object to matching()
        return $this->answers->matching(AnswerRepository::createApprovedCriteria());
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuestionTag>
     */
    public function getQuestionTags(): Collection
    {
        return $this->questionTags;
    }

    // hasTag() helper to check if a passed in tag has already been assigned to a questionTag
    public function hasTag(Tag $tag): bool
    {
        foreach($this->questionTags as $questionTag) {
            if ($questionTag->getTag() === $tag) {
                return true;
            }
        }

        return false;
    }

    public function addQuestionTag(QuestionTag $questionTag): self
    {
        if (!$this->questionTags->contains($questionTag)) {
            $this->questionTags[] = $questionTag;
            $questionTag->setQuestion($this);
        }

        return $this;
    }

    public function removeQuestionTag(QuestionTag $questionTag): self
    {
        if ($this->questionTags->removeElement($questionTag)) {
            // set the owning side to null (unless already changed)
            if ($questionTag->getQuestion() === $this) {
                $questionTag->setQuestion(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
