<?php

namespace Hgabka\SimpleContentBundle\Tests\Admin;

use Hgabka\SimpleContentBundle\Admin\SimpleContentAdmin;
use Hgabka\SimpleContentBundle\Entity\SimpleContent;
use Hgabka\SimpleContentBundle\Helper\SimpleContentManager;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use PHPUnit\Framework\TestCase;

class SimpleContentAdminTest extends TestCase
{
    private function admin(?SimpleContentManager $manager = null): SimpleContentAdmin
    {
        $admin = new SimpleContentAdmin();
        $admin->setManager($manager ?? $this->createStub(SimpleContentManager::class));

        return $admin;
    }

    public function testCacheIsUpdatedAfterPersistAndUpdate(): void
    {
        $content = new SimpleContent();
        $manager = $this->createMock(SimpleContentManager::class);
        $manager->expects($this->exactly(2))->method('addContentToCache')->with($content);

        $admin = $this->admin($manager);
        $admin->postPersist($content);
        $admin->postUpdate($content);
    }

    public function testCacheEntryIsRemovedBeforeDelete(): void
    {
        $content = new SimpleContent();
        $manager = $this->createMock(SimpleContentManager::class);
        $manager->expects($this->once())->method('deleteContentFromCache')->with($content);

        $this->admin($manager)->preRemove($content);
    }

    public function testToStringUsesTheDescriptionInTheCurrentLocale(): void
    {
        $content = new SimpleContent();
        $content->translate('hu')->setDescription('Lábléc');
        $utils = $this->createStub(HgabkaUtils::class);
        $utils->method('getCurrentLocale')->willReturn('hu');
        $admin = $this->admin()->setUtils($utils);

        $this->assertSame('Lábléc', $admin->toString($content));
    }

    public function testSettersAreFluent(): void
    {
        $admin = $this->admin();

        $this->assertSame($admin, $admin->setEditorMode('basic'));
    }

    public function testCssFilesAreFilteredToUrlsAndAbsolutePaths(): void
    {
        $content = (new SimpleContent())->setCssFiles("/css/a.css\n  https://cdn.example/b.css \nrelative.css\nhttp://x/c.css");

        $result = (new \ReflectionMethod(SimpleContentAdmin::class, 'getCssFilesArray'))->invoke($this->admin(), $content);

        $this->assertSame(['/css/a.css', 'https://cdn.example/b.css', 'http://x/c.css'], $result);
    }

    public function testNoCssFilesGiveEmptyList(): void
    {
        $result = (new \ReflectionMethod(SimpleContentAdmin::class, 'getCssFilesArray'))->invoke($this->admin(), new SimpleContent());

        $this->assertSame([], $result);
    }
}
