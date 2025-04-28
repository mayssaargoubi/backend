<?php

namespace App\Entity;

use App\Repository\ObjectifNoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ObjectifNoteRepository::class)]
class ObjectifNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Objectif::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Objectif $objectif = null;

    #[ORM\ManyToOne(targetEntity: Evaluation::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evaluation $evaluation = null;

    #[ORM\Column(type: "float")]
    private ?float $note = 0;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $statut = 'attente';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectif(): ?Objectif
    {
        return $this->objectif;
    }

    public function setObjectif(?Objectif $objectif): self
    {
        $this->objectif = $objectif;

        return $this;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
}
