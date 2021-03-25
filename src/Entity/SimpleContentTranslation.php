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
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @var null|SimpleContent
     *
     * @Hgabka\Translatable(targetEntity=SimpleContent::class)
     */
    private $translatable;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return SimpleContentTranslation
     */
    public function setDescription(?string $description): SimpleContentTranslation
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     * @return SimpleContentTranslation
     */
    public function setValue(?string $value): SimpleContentTranslation
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return SimpleContent|null
     */
    public function getTranslatable(): ?SimpleContent
    {
        return $this->translatable;
    }
}
