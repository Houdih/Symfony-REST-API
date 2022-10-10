<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:comments']],      
    denormalizationContext: ['groups' => ['write:comment']],       
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 20,
    paginationClientItemsPerPage: true,
    collectionOperations: [
        'get',
        "post" => [
            "security_post_denormalize" => "is_granted('COMMENT_CREATE', object)",
            "security_post_denormalize_message" => "Only users can add Articles.",
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'openapi_definition_name' => 'item'
            ],            
        ],
        "put" => [
            "security" => "is_granted('COMMENT_EDIT', object)",
            "security_message" => "Sorry, but you are not the actual Comment owner."
        ],
        'delete' => [
            "security" => "is_granted('COMMENT_DELETE', object)",
            "security_message" => "Sorry, but you are not the actual Comment owner."
        ],
    ],
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:comments', 'read:articles', 'read:users'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:comments', 'write:comment', 'read:articles'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['read:comments', 'read:articles'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd-m-Y h:i:s'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:comments', 'read:articles'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['write:comment'])]
    private ?Article $article = null;


    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:comments', 'write:user'])]
    private ?User $authorComment = null;
    

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAuthorComment(): ?User
    {
        return $this->authorComment;
    }

    public function setAuthorComment(?User $authorComment): self
    {
        $this->authorComment = $authorComment;

        return $this;
    }
}
