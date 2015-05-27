<?php

namespace Dwo\FlaggingBundle\Cache;

use Doctrine\Common\Cache\Cache;
use Dwo\ConfigPrototype\Model\ConfigPrototype;
use Dwo\ConfigPrototype\Model\ConfigPrototypeInterface;
use Dwo\ConfigPrototype\Model\ConfigPrototypeManagerInterface;

/**
 * Class ConfigPrototypeManager
 *
 * @author David Wolter <david@lovoo.com>
 */
class ConfigPrototypeManager implements ConfigPrototypeManagerInterface
{
    const NAME = 'dwo:feature:%s';

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function findConfigPrototypeByNameAndType($name, $type)
    {
        $configPrototype = null;

        if ($this->cache->contains(sprintf(self::NAME, $name))) {
            $featureRaw = $this->cache->fetch(sprintf(self::NAME, $name));
            $configPrototype = new ConfigPrototype();
            $configPrototype->setType($type);
            $configPrototype->setName($name);
            $configPrototype->setContent($featureRaw);
        }

        return $configPrototype;
    }

    /**
     * {@inheritdoc}
     */
    public function saveConfigPrototype(ConfigPrototypeInterface $configPrototype)
    {
        $this->cache->save(sprintf(self::NAME, $configPrototype->getName()), $configPrototype->getContent());
    }
}
