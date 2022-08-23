<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;


#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => ['read:articles'],
        'openapi_definition_name' => 'collection'
    ],
    denormalizationContext: ['groups' => ['write:article']],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 20,
    paginationClientItemsPerPage: true,
    itemOperations: [
        'put',
        'delete',
        'get' => [
            'normalization_context' => [
                'groups' => ['read:articles', 'read:article'],
                'openapi_definition_name' => 'item'
            ],            
        ]
    ]
)]
class Article 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read:articles')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(['read:articles', 'write:article']),
        Length(min:5)
    ]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:articles'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:article', 'write:article'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups('read:articles')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd-m-Y h:i:s'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(options: ["default" => 0])]
    #[Groups(['read:articles'])]
    private ?bool $isPublished = false;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, orphanRemoval: true)]
    #[ApiSubresource()]
    #[Groups(['read:articles'])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'articles', cascade: ['persist'])]
    #[
        Groups(['read:articles', 'write:article']),
        Valid()
    ]
    private Collection $categories;

    public function __construct() {
        $this->createdAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
