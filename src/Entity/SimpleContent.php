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
 */
class SimpleContent implements TranslatableInterface
{
    use TranslatableTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $css;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $cssFiles;

    /**
     * @var int|null
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
     * @return SimpleContent
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCss(): ?string
    {
        return $this->css;
    }

    /**
     * @param string|null $css
     * @return SimpleContent
     */
    public function setCss(?string $css): SimpleContent
    {
        $this->css = $css;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCssFiles(): ?string
    {
        return $this->cssFiles;
    }

    /**
     * @param string|null $cssFiles
     * @return SimpleContent
     */
    public function setCssFiles(?string $cssFiles): SimpleContent
    {
        $this->cssFiles = $cssFiles;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     * @return SimpleContent
     */
    public function setWidth(?int $width): SimpleContent
    {
        $this->width = $width;

        return $this;
    }


    public static function getTranslationEntityClass()
    {
        return SettingTranslation::class;
    }
}
