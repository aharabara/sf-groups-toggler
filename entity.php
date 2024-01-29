<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    paginationType: 'page'
)]
class Article
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $fullTitle = null;

    #[ORM\Column]
    private array $fullAbstract = [];

    #[ORM\Column(length: 255)]
    private ?string $shortTitle = null;

    #[ORM\Column]
    private array $shortAbstract = [];

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column]
    private array $content = [];

    #[ORM\Column(length: 255)]
    private ?string $authorSignature = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $firstPublishedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastPublishedAt = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?ArticleCategory $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getCategory(): ?ArticleCategory
    {
        return $this->category;
    }

    public function setCategory(?ArticleCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getFullTitle(): ?string
    {
        return $this->fullTitle;
    }

    public function setFullTitle(string $fullTitle): static
    {
        $this->fullTitle = $fullTitle;

        return $this;
    }

    public function getShortTitle(): ?string
    {
        return $this->shortTitle;
    }

    public function setShortTitle(string $shortTitle): static
    {
        $this->shortTitle = $shortTitle;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getFullAbstract(): array
    {
        return $this->fullAbstract;
    }

    public function setFullAbstract(array $fullAbstract): static
    {
        $this->fullAbstract = $fullAbstract;

        return $this;
    }

    public function getShortAbstract(): array
    {
        return $this->shortAbstract;
    }

    public function setShortAbstract(array $shortAbstract): static
    {
        $this->shortAbstract = $shortAbstract;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthorSignature(): ?string
    {
        return $this->authorSignature;
    }

    public function setAuthorSignature(string $authorSignature): static
    {
        $this->authorSignature = $authorSignature;

        return $this;
    }

    public function getFirstPublishedAt(): ?\DateTimeImmutable
    {
        return $this->firstPublishedAt;
    }

    public function setFirstPublishedAt(?\DateTimeImmutable $firstPublishedAt): static
    {
        $this->firstPublishedAt = $firstPublishedAt;

        return $this;
    }

    public function getLastPublishedAt(): ?\DateTimeImmutable
    {
        return $this->lastPublishedAt;
    }

    public function setLastPublishedAt(?\DateTimeImmutable $lastPublishedAt): static
    {
        $this->lastPublishedAt = $lastPublishedAt;

        return $this;
    }
}
