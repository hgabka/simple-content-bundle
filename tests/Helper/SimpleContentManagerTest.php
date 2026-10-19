<?php

namespace Hgabka\SimpleContentBundle\Tests\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Hgabka\SimpleContentBundle\Entity\SimpleContent;
use Hgabka\SimpleContentBundle\Helper\SimpleContentManager;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use PHPUnit\Framework\TestCase;

class SimpleContentManagerTest extends TestCase
{
    private string $cacheDir;

    /** @var array<string, SimpleContent> */
    private array $store = [];

    private int $lookups = 0;

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . '/hg_simple_content_' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->cacheDir);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }

    private function content(string $name, array $values): SimpleContent
    {
        $content = (new SimpleContent())->setName($name);
        foreach ($values as $locale => $value) {
            $content->translate($locale)->setValue($value);
        }

        return $this->store[$name] = $content;
    }

    private function manager(): SimpleContentManager
    {
        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findOneBy')->willReturnCallback(function (array $criteria): ?SimpleContent {
            ++$this->lookups;

            return $this->store[$criteria['name']] ?? null;
        });
        $doctrine = $this->createStub(Registry::class);
        $doctrine->method('getRepository')->willReturn($repository);

        $utils = $this->createStub(HgabkaUtils::class);
        $utils->method('getAvailableLocales')->willReturn(['hu', 'en']);
        $utils->method('getCurrentLocale')->willReturnCallback(static fn (?string $locale = null): string => $locale ?? 'hu');

        return new SimpleContentManager($doctrine, $utils, $this->cacheDir);
    }

    public function testUnknownContentIsEmptyString(): void
    {
        $this->assertSame('', $this->manager()->getContent('missing'));
    }

    public function testContentIsLoadedFromTheDatabaseOnceAndThenCached(): void
    {
        $this->content('footer', ['hu' => 'Lábléc', 'en' => 'Footer']);
        $manager = $this->manager();

        $this->assertSame('Lábléc', $manager->getContent('footer'));
        $this->assertSame('Footer', $manager->getContent('footer', [], 'en'));
        $this->assertSame('Lábléc', $manager->getContent('footer'));
        $this->assertSame(1, $this->lookups);
    }

    public function testCacheSurvivesAcrossManagerInstances(): void
    {
        $this->content('footer', ['hu' => 'Lábléc', 'en' => 'Footer']);
        $this->manager()->getContent('footer');
        $this->lookups = 0;

        $this->assertSame('Lábléc', $this->manager()->getContent('footer'));
        $this->assertSame(0, $this->lookups);
    }

    public function testPlaceholdersAreReplaced(): void
    {
        $this->content('hello', ['hu' => 'Szia %name%!', 'en' => 'Hi %name%!']);
        $manager = $this->manager();

        $this->assertSame('Szia Éva!', $manager->getContent('hello', ['%name%' => 'Éva']));
        // the second call is answered from the cache, replacement must still happen
        $this->assertSame('Hi Ann!', $manager->getContent('hello', ['%name%' => 'Ann'], 'en'));
    }

    public function testRemoveFromCacheForcesReload(): void
    {
        $content = $this->content('footer', ['hu' => 'Régi', 'en' => 'Old']);
        $manager = $this->manager();
        $manager->getContent('footer');

        $content->translate('hu')->setValue('Új');
        $this->assertSame('Régi', $manager->getContent('footer'));

        $this->assertTrue($manager->removeFromCache('footer'));
        $this->assertSame('Új', $manager->getContent('footer'));
    }

    public function testAddContentToCacheOverwritesStoredValue(): void
    {
        $content = $this->content('footer', ['hu' => 'Régi', 'en' => 'Old']);
        $manager = $this->manager();
        $manager->getContent('footer');

        $content->translate('hu')->setValue('Új');
        $this->assertTrue($manager->addContentToCache($content));

        $this->assertSame(['hu' => 'Új', 'en' => 'Old'], $manager->getCacheData()['footer']);
    }

    public function testDeleteContentFromCache(): void
    {
        $content = $this->content('footer', ['hu' => 'a', 'en' => 'b']);
        $manager = $this->manager();
        $manager->getContent('footer');

        $manager->deleteContentFromCache($content);

        $this->assertArrayNotHasKey('footer', $manager->getCacheData());
    }

    public function testRemovingUnknownNameIsHarmless(): void
    {
        $this->assertTrue($this->manager()->removeFromCache('nothing'));
    }

    public function testLocalesComeFromUtils(): void
    {
        $this->assertSame(['hu', 'en'], $this->manager()->getLocales());
    }
}
