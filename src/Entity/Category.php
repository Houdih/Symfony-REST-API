<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:categories']],
    denormalizationContext: ['groups' => ['write:category']],
    collectionOperations: [
        'get',
        "post" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN', 'ROLE_AUTHOR')",
            "security_post_denormalize_message" => "Only Admins and Authors can add Category.",
        ],
    ],
    itemOperations: [
        'get',
        "put" => [
            "security" => "is_granted('ROLE_ADMIN', 'ROLE_AUTHOR')",
            "security_message" => "Only Admins and Authors can put Category."
        ],
        'delete' => [
            "security" => "is_granted('ROLE_ADMIN', 'ROLE_AUTHOR')",
            "security_message" => "Only Admins and Authors can delete Category."
        ],
    ],
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read:article')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[
        Groups(['read:categories', 'write:category', 'read:articles', 'write:article']),
        Length(min:3)
    ]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'categories')]
    #[ApiSubresource()]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
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

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addCategory($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeCategory($this);
        }

        return $this;
    }
}
