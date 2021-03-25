<?php

namespace Hgabka\SimpleContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslationTrait;
use Hgabka\Doctrine\Translatable\TranslationInterface;

/**
 * @ORM\Table(name="hg_simple_content_translation")
 * @ORM\Entity
 */
class SimpleContentTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @var null|SimpleContent
     *
     * @Hgabka\Translatable(targetEntity=SimpleContent::class)
     */
    private $translatable;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getTranslatable(): ?SimpleContent
    {
        return $this->translatable;
    }
}
