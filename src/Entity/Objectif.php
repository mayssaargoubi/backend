<?php

namespace App\Entity;

use App\Repository\ObjectifRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: ObjectifRepository::class)]
class Objectif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
private ?\DateTimeInterface $dateCreation = null;

#[ORM\Column(type: 'datetime')]
private ?\DateTimeInterface $dateEcheance = null;


    #[ORM\Column(options: ["default" => 0])]
    private ?float $poid = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $manager = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $employee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
{
    return $this->dateCreation;
}

public function setDateCreation(\DateTimeInterface $dateCreation): self
{
    $this->dateCreation = $dateCreation;
    return $this;
}

public function getDateEcheance(): ?\DateTimeInterface
{
    return $this->dateEcheance;
}

public function setDateEcheance(\DateTimeInterface $dateEcheance): self
{
    $this->dateEcheance = $dateEcheance;
    return $this;
}


    public function getPoid(): ?float
    {
        return $this->poid;
    }

    public function setPoid(float $poid): static
    {
        $this->poid = $poid;

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }
}
