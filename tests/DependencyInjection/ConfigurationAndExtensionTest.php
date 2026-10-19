<?php

namespace Hgabka\SimpleContentBundle\Tests\DependencyInjection;

use Hgabka\SimpleContentBundle\DependencyInjection\Configuration;
use Hgabka\SimpleContentBundle\DependencyInjection\HgabkaSimpleContentExtension;
use Hgabka\SimpleContentBundle\Helper\SimpleContentManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationAndExtensionTest extends TestCase
{
    public function testEditorModeIsNullByDefault(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), []);

        $this->assertSame(['editor_mode' => null], $config);
    }

    public function testExtensionExposesEditorModeAndRegistersServices(): void
    {
        $container = new ContainerBuilder();
        (new HgabkaSimpleContentExtension())->load([['editor_mode' => 'basic']], $container);

        $this->assertSame('basic', $container->getParameter('hgabka_simple_content.editor_mode'));
        $this->assertTrue($container->hasDefinition('hg_simple_content.simple_content_manager'));
        $this->assertSame(SimpleContentManager::class, $container->getDefinition('hg_simple_content.simple_content_manager')->getClass());
        $this->assertArrayHasKey('twig.extension', $container->getDefinition('hg_simple_content.simple_content_twig_extension')->getTags());
        $this->assertTrue($container->hasAlias(SimpleContentManager::class));
    }
}
