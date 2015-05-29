<?php

namespace Dwo\FlaggingBundle\Cache;

use Doctrine\Common\Cache\Cache;
use Dwo\Flagging\Factory\FeatureFactory;
use Dwo\Flagging\Model\FeatureInterface;
use Dwo\Flagging\Model\FeatureManagerInterface;
use Dwo\Flagging\Serializer\FeatureSerializer;

/**
 * @author Dave Www <davewwwo@gmail.com>
 */
class FeatureManager implements FeatureManagerInterface
{
    const NAME = 'dwo:feature:%s';

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var FeatureManagerInterface
     */
    protected $delegatedFeatureManger;

    /**
     * @var FeatureInterface[]
     */
    protected $features;

    /**
     * @param Cache                   $cache
     * @param FeatureManagerInterface $delegatedFeatureManger
     */
    public function __construct(Cache $cache, FeatureManagerInterface $delegatedFeatureManger)
    {
        $this->cache = $cache;
        $this->delegatedFeatureManger = $delegatedFeatureManger;
    }

    /**
     * {@inheritdoc}
     */
    public function findFeatureByName($name)
    {
        if (!isset($this->features[$name])) {

            if ($this->cache->contains(sprintf(self::NAME, $name))) {
                $featureRaw = $this->cache->fetch(sprintf(self::NAME, $name));
                $feature = FeatureFactory::buildFeature($name, $featureRaw);
            } else {
                if (null !== $feature = $this->delegatedFeatureManger->findFeatureByName($name)) {
                    $this->cache->save(sprintf(self::NAME, $name), FeatureSerializer::serialize($feature));
                }
            }

            $this->features[$name] = $feature;
        } else {
            $feature = $this->features[$name];
        }

        return $feature;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllFeatures()
    {
        return $this->delegatedFeatureManger->findAllFeatures();
    }

    /**
     * {@inheritdoc}
     */
    public function saveFeature(FeatureInterface $feature)
    {
        $this->delegatedFeatureManger->saveFeature($feature);
    }
}
