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
    collectionOperations: [
        'get',
        "post" => [
            "security_post_denormalize" => "is_granted('ARTICLE_CREATE', object)",
            "security_post_denormalize_message" => "Only Admins and Authors can add Articles.",
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'openapi_definition_name' => 'item',
                'groups' => ['read:articles','read:article']
            ],            
        ],
        "put" => [
            "security" => "is_granted('ARTICLE_EDIT', object)",
            "security_message" => "Sorry, but you are not the actual Article owner."
        ],
        'delete' => [
            "security" => "is_granted('ARTICLE_DELETE', object)",
            "security_message" => "Sorry, but you are not the actual Article owner."
        ],
    ],
)]
class Article 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:articles', 'read:users'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(['read:articles', 'write:article', 'read:users']),
        Length(min:5)
    ]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:articles'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:article', 'read:articles', 'write:article'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups('read:articles', 'read:users')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd-m-Y h:i:s'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(options: ["default" => 0])]
    #[Groups(['read:articles', 'read:users'])]
    private ?bool $isPublished = false;

    #[ORM\ManyToOne(targetEntity: MediaObject::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read:articles','write:article'])]
    public ?MediaObject $img = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, orphanRemoval: true)]
    #[ApiSubresource()]
    #[Groups(['read:articles'])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'articles', cascade: ['persist'])]
    #[
        Groups(['read:articles', 'write:article', 'read:users']),
        Valid()
    ]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:articles'])]
    private ?User $authorArticle = null;

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

    public function getImg(): ?MediaObject
    {
        return $this->img;
    }

    public function setImg(MediaObject $img): self
    {
        $this->img = $img;

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

    public function getAuthorArticle(): ?User
    {
        return $this->authorArticle;
    }

    public function setAuthorArticle(?User $authorArticle): self
    {
        $this->authorArticle = $authorArticle;

        return $this;
    }
}
