<?php

namespace Hgabka\SimpleContentBundle\Admin;

use Hgabka\SimpleContentBundle\Helper\SimpleContentManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;

class SimpleContentAdmin extends AbstractAdmin
{
    /** @var SimpleContentManager */
    protected $manager;

    public function setManager(SimpleContentManager $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function preRemove($object)
    {
        $this->manager->deleteContentFromCache($object);
    }

    public function postPersist($object)
    {
        $this->manager->addContentToCache($object);
    }

    public function postUpdate($object)
    {
        $this->manager->addContentToCache($object);
    }
}
