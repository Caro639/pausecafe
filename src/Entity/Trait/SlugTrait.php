<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait SlugTrait
{
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}