<?php

namespace Hgabka\SimpleContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\Doctrine\Translatable\Annotation as Hgabka;
use Hgabka\Doctrine\Translatable\TranslatableInterface;
use Hgabka\UtilsBundle\Traits\TimestampableEntity;
use Hgabka\UtilsBundle\Traits\TranslatableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Setting.
 *
 * @ORM\Table(name="hg_simple_content")
 * @ORM\Entity(repositoryClass="Hgabka\SimpleContentBundle\Repository\SimpleContentRepository")
 * @UniqueEntity(fields={"name"}, message="A megadott névvel már létezik tartalom", errorPath="name")
 */
class SimpleContent implements TranslatableInterface
{
    use TimestampableEntity;
    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $css;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $cssFiles;

    /**
     * @var null|int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $width;

    /**
     * @Hgabka\Translations(targetEntity="Hgabka\SimpleContentBundle\Entity\SimpleContentTranslation")
     */
    private $translations;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->getDescription();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Setting
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return SimpleContent
     */
    public function setName($name)
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

    public static function getTranslationEntityClass()
    {
        return SimpleContentTranslation::class;
    }

    public function getDescription($locale = null)
    {
        return $this->translate($locale)->getDescription();
    }
}
