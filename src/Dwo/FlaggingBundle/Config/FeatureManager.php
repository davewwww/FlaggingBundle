<?php

namespace Dwo\FlaggingBundle\Config;

use Dwo\Flagging\Factory\FeatureFactory;
use Dwo\Flagging\Model\FeatureInterface;
use Dwo\Flagging\Model\FeatureManagerInterface;

/**
 * @author David Wolter <david@lovoo.com>
 */
class FeatureManager implements FeatureManagerInterface
{
    /**
     * @var array
     */
    protected $featuresRaw;

    /**
     * @var FeatureInterface[]
     */
    protected $features;

    /**
     * @param array $features
     */
    public function __construct(array $features = array())
    {
        $this->featuresRaw = $features;
    }

    /**
     * @param string $name
     *
     * @return FeatureInterface
     */
    public function findFeatureByName($name)
    {
        if (!isset($this->features[$name]) && isset($this->featuresRaw[$name])) {
            $this->features[$name] = FeatureFactory::buildFeature($name, $this->featuresRaw[$name]);
        }

        return isset($this->features[$name]) ? $this->features[$name] : null;
    }

    /**
     * @return FeatureInterface[]
     */
    public function findAllFeatures()
    {
        $features = [];
        foreach ($this->featuresRaw as $name => $data) {
            if (null !== $feature = $this->findFeatureByName($name)) {
                $features[$name] = $feature;
            }
        }

        return $features;
    }

    /**
     * @param FeatureInterface $feature
     */
    public function saveFeature(FeatureInterface $feature)
    {
        $this->features[$feature->getName()] = $feature;
    }
}
