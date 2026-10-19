<?php

namespace Hgabka\SimpleContentBundle\Tests\Entity;

use Hgabka\SimpleContentBundle\Entity\SimpleContent;
use Hgabka\SimpleContentBundle\Entity\SimpleContentTranslation;
use PHPUnit\Framework\TestCase;

class SimpleContentTest extends TestCase
{
    public function testScalarAccessorsAreFluent(): void
    {
        $content = new SimpleContent();

        $this->assertSame($content, $content->setName('footer')->setCss('p{}')->setCssFiles('/a.css')->setWidth(600)->setId(3));
        $this->assertSame('footer', $content->getName());
        $this->assertSame('p{}', $content->getCss());
        $this->assertSame('/a.css', $content->getCssFiles());
        $this->assertSame(600, $content->getWidth());
        $this->assertSame(3, $content->getId());
    }

    public function testTranslationEntityClass(): void
    {
        $this->assertSame(SimpleContentTranslation::class, SimpleContent::getTranslationEntityClass());
    }

    public function testTranslateCreatesAndReusesTranslationPerLocale(): void
    {
        $content = new SimpleContent();

        $hu = $content->translate('hu');
        $this->assertInstanceOf(SimpleContentTranslation::class, $hu);
        $this->assertSame('hu', $hu->getLocale());
        $this->assertSame($hu, $content->translate('hu'));
        $this->assertNotSame($hu, $content->translate('en'));
        $this->assertCount(2, $content->getTranslations());
    }

    public function testTranslateWithoutLocaleNeedsCurrentLocale(): void
    {
        $this->expectException(\RuntimeException::class);
        (new SimpleContent())->translate();
    }

    public function testDescriptionIsReadFromTheTranslation(): void
    {
        $content = new SimpleContent();
        $content->translate('hu')->setDescription('Lábléc');
        $content->setCurrentLocale('hu');

        $this->assertSame('Lábléc', $content->getDescription());
        $this->assertSame('Lábléc', $content->getDescription('hu'));
        $this->assertSame('Lábléc', (string) $content);
        $this->assertNull($content->getDescription('en'));
    }

    public function testTranslationValue(): void
    {
        $translation = (new SimpleContentTranslation())->setValue('<p>x</p>');

        $this->assertSame('<p>x</p>', $translation->getValue());
    }
}
