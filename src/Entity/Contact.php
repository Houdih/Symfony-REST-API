<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ContactRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:contact']],
    denormalizationContext: ['groups' => ['write:contact']],
    collectionOperations: [
        'get',
        "post",
    ],
    itemOperations: [
        'get',
        "put",
        'delete' => [
            "security" => "is_granted('ROLE_ADMIN')",
            "security_message" => "Désolé vous n'avez pas les droits pour supprimer cet utilisateur !",
        ],
    ],
)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:contact'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:contact', 'write:contact'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:contact', 'write:contact'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:contact', 'write:contact'])]
    private ?string $entreprise = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:contact', 'write:contact'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:contact', 'write:contact'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['read:contact'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y à H:i'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct() {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(string $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
