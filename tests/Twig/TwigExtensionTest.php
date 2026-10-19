<?php

namespace Hgabka\SimpleContentBundle\Tests\Twig;

use Hgabka\SimpleContentBundle\Helper\SimpleContentManager;
use Hgabka\SimpleContentBundle\Twig\HgabkaSimpleContentTwigExtension;
use PHPUnit\Framework\TestCase;

class TwigExtensionTest extends TestCase
{
    public function testRenderSimpleContentDelegatesToManager(): void
    {
        $manager = $this->createMock(SimpleContentManager::class);
        $manager->expects($this->once())->method('getContent')->with('footer', ['%a%' => 'b'], 'en')->willReturn('<p>x</p>');

        $extension = new HgabkaSimpleContentTwigExtension($manager);

        $this->assertSame('<p>x</p>', $extension->renderSimpleContent('footer', ['%a%' => 'b'], 'en'));
    }

    public function testFunctionIsRegisteredAsHtmlSafe(): void
    {
        $functions = (new HgabkaSimpleContentTwigExtension($this->createStub(SimpleContentManager::class)))->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertSame('render_simple_content', $functions[0]->getName());
        $this->assertSame(['html'], $functions[0]->getSafe(new \Twig\Node\Node()));
    }
}
