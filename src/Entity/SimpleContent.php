<?php

namespace Hgabka\SimpleContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\SimpleContentBundle\Repository\SimpleContentRepository;
use Hgabka\UtilsBundle\Traits\TimestampableEntity;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SimpleContentRepository::class)]
#[ORM\Table(name: 'hg_simple_content')]
#[UniqueEntity(fields: ['name'], message: 'A megadott névvel már létezik tartalom', errorPath: 'name')]
class SimpleContent implements TranslatableInterface
{
    use TimestampableEntity;
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    protected ?string $name = null;

    #[ORM\Column(name: 'css', type: 'text', nullable: true)]
    protected ?string $css = null;

    #[ORM\Column(name: 'css_files', type: 'text', nullable: true)]
    protected ?string $cssFiles = null;

    #[ORM\Column(name: 'width', type: 'integer', nullable: true)]
    protected ?int $width = null;

    /**
     * @Hgabka\Translations(targetEntity="Hgabka\SimpleContentBundle\Entity\SimpleContentTranslation")
     */
    #[Hgabka\Translations(targetEntity: SimpleContentTranslation::class)]
    private Collection|array|null $translations = null;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getDescription();
    }

    public function getId(): ?id
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCss(): ?string
    {
        return $this->css;
    }

    public function setCss(?string $css): self
    {
        $this->css = $css;

        return $this;
    }

    public function getCssFiles(): ?string
    {
        return $this->cssFiles;
    }

    public function setCssFiles(?string $cssFiles): self
    {
        $this->cssFiles = $cssFiles;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public static function getTranslationEntityClass(): string
    {
        return SimpleContentTranslation::class;
    }

    public function getDescription(?string $locale = null): ?string
    {
        return $this->translate($locale)->getDescription();
    }
}
