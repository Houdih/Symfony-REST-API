<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => ['read:comments'],
    ],
    denormalizationContext: [
        ['groups' => ['write:comment']],
    ]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:comments', 'read:article'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:comments', 'write:comment', 'read:article'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['read:comments', 'read:article'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['write:comment'])]
    private ?Article $article = null;


    public function __construct() {
        $this->createdAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
