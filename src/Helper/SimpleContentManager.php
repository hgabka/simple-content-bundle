<?php

namespace Hgabka\SimpleContentBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SimpleContentBundle\Entity\SimpleContent;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class SimpleContentManager
{
    const CACHE_KEY = 'simplecontent';
    /**
     * @var Registry
     */
    protected $doctrine;

    /** @var HgabkaUtils */
    protected $utils;
    /**
     * @var string
     */
    protected $cacheDir;
    /**
     * @var array
     */
    protected $settings;
    /**
     * @var FilesystemAdapter
     */
    protected $cache;

    /**
     * @var array
     */
    protected $types = [];

    /**
     * SettingsManager constructor.
     *
     * @param Registry $doctrine
     * @param $cacheDir
     */
    public function __construct(ManagerRegistry $doctrine, HgabkaUtils $utils, string $cacheDir)
    {
        $this->doctrine = $doctrine;
        $this->cacheDir = $cacheDir;
        $this->utils = $utils;
    }


    /**
     * Az összes beállítás lekérdezése a cache-ből.
     *
     * @return array
     */
    public function getCacheData()
    {
        $cache = $this->getCache();
        if (!$cache->hasItem(self::CACHE_KEY)) {
            return $this->regenerateCache();
        }

        return $cache->getItem(self::CACHE_KEY)->get() ?? [];
    }

    /**
     * Beállítás hozzáadása a cache-hez.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function addContentToCache(SimpleContent $simpleContent)
    {
        $cache = $this->getCache();
        if ($cache->hasItem(self::CACHE_KEY)) {
            $item = $cache->getItem(self::CACHE_KEY);

            $data = $item->get() ?? [];
            $value = [];
            foreach ($this->getLocales() as $locale) {
                $value[$locale] = $simpleContent->translate($locale)->getValue();
            }
            $data[$simpleContent->getName()] = $value;

            $item->set($data);

            return $cache->save($item);
        }

        return true;
    }

    protected function getCachedValue(string $name)
    {
        $cache = $this->getCache();
        if ($cache->hasItem(self::CACHE_KEY)) {
            $data = $item->get() ?? [];

            return $data[$name] ?? null;
        }

        return null;
    }

    /**
     * Beállítás törlése a cache-ből.
     *
     * @param string $name
     *
     * @return bool
     */
    public function removeFromCache($name)
    {
        $cache = $this->getCache();
        if ($cache->hasItem(self::CACHE_KEY)) {
            $item = $cache->getItem(self::CACHE_KEY);
            $data = $item->get() ?? [];
            if (\array_key_exists($name, $data)) {
                unset($data[$name]);
            }
            $item->set($data);

            return $cache->save($item);
        }

        return true;
    }

    public function deleteContentFromCache(SimpleContent $content)
    {
        $this->removeFromCache($content->getName());
    }

    public function clearCache()
    {
        $cache = $this->getCache();

        $cache->clear();
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->utils->getAvailableLocales();
    }

    public function getContent(string $name, array $params = [], ?string $locale = null): string
    {
        $locale = $this->utils->getCurrentLocale($locale);
        $cachedValue = $this->getCachedValue($name);

        if (is_array($cachedValue) && array_key_exists($locale, $cachedValue)) {
            return strtr($cachedValue[$locale], $params);
        }

        $content = $this->doctrine->getRepository(SimpleContent::class)->findOneBy(['name' => $name]);

        if (!$content) {
            return '';
        }

        $this->addContentToCache($content);

        return strtr($content->translate($locale)->getValue(), $params);
    }
}
