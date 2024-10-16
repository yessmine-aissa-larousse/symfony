<?php

namespace App\Entity;

use App\Repository\CategorySearchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorySearchRepository::class)]
class CategorySearch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

        /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     */
    private $category;
    public function getCategory(): ?Category
    {
    return $this->category;
    }
    public function setCategory(?Category $category): self
    {$this->category = $category;
    return $this; }

    public function getId(): ?int
    {
        return $this->id;
    }
}
