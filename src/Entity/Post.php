<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as Store;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['read:Post', 'read:item:Post'],
            ],
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => ['read:Post', 'read:collection:Post'],
            ],
        ),
        new Patch(),
        new Store(),
    ],
    // normalizationContext: [
    // 'groups' => ['read:Post'],
    // ], // GET
    denormalizationContext: [
        'groups' => ['write:Post'],
    ], // POST, PUT, PATCH
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title'         => 'partial', // exact, partial, start, end, word_start
    'body'          => 'partial',
    'category.name' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['id'])]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Post'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Post', 'write:Post'])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:item:Post', 'write:Post'])]
    #[Assert\NotBlank]
    private ?string $body = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Post', 'write:Post'])]
    #[Assert\NotBlank]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    #[Groups(['read:collection:Post'])]
    public function getSummary(): ?string
    {
        if(mb_strlen($this->body) <= 70) {
            return $this->body;
        }
        return mb_substr($this->body, 0, 70) . '...';
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
