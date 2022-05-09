<?php

namespace Hgabka\SimpleContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\Entity\TranslationTrait;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\Doctrine\Translatable\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'hg_simple_content_translation')]
class SimpleContentTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(name: 'value', type: 'text', nullable: true)]
    protected ?string $value = null;

    /**
     * @Hgabka\Translatable(targetEntity=SimpleContent::class)
     */
    #[Hgabka\Translatable(targetEntity: SimpleContent::class)]
    private ?TranslatableInterface $translatable = null;

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
