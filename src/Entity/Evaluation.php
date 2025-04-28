<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $manager = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateEvaluation = null;

    #[ORM\Column(length: 20)]
    private ?string $periode = null;

    #[ORM\Column(type: 'float')]
    private float $noteGlobale = 0;

    #[ORM\Column(length: 20)]
    private ?string $statut = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null;

    // Getters & Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): self
    {
        $this->manager = $manager;
        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getDateEvaluation(): ?\DateTimeInterface
    {
        return $this->dateEvaluation;
    }

    public function setDateEvaluation(\DateTimeInterface $dateEvaluation): self
    {
        $this->dateEvaluation = $dateEvaluation;
        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(string $periode): self
    {
        $this->periode = $periode;
        return $this;
    }

    public function getNoteGlobale(): float
    {
        return $this->noteGlobale;
    }

    public function setNoteGlobale(float $noteGlobale): self
    {
        $this->noteGlobale = $noteGlobale;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }
    #[ORM\Column(type: 'datetime')]
private ?\DateTimeInterface $dateCreation = null;

public function getDateCreation(): ?\DateTimeInterface
{
    return $this->dateCreation;
}

public function setDateCreation(\DateTimeInterface $dateCreation): self
{
    $this->dateCreation = $dateCreation;
    return $this;
}

}
