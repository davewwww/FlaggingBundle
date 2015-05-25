<?php

namespace Dwo\FlaggingBundle\Tests\Cache;

use Doctrine\Common\Cache\Cache;
use Dwo\Flagging\Model\Feature;
use Dwo\Flagging\Model\FeatureManagerInterface;
use Dwo\FlaggingBundle\Cache\FeatureManager;

class FeatureManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCacheSave()
    {
        $cache = $this->mockCache();
        $delegatedManager = $this->mockFeatureManager();

        $cache->expects(self::once())
            ->method('contains')
            ->willReturn(false);

        $cache->expects(self::never())
            ->method('fetch');

        $cache->expects(self::once())
            ->method('save');

        $delegatedManager->expects(self::once())
            ->method('findFeatureByName')
            ->with('foo')
            ->willReturn($feature = new Feature('foo'));

        $manager = new FeatureManager($cache, $delegatedManager);
        $result = $manager->findFeatureByName('foo');

        self::assertEquals($result, $feature);
    }

    /**
     * @dataProvider providerCount
     */
    public function testCacheRead($count)
    {
        $cache = $this->mockCache();
        $delegatedManager = $this->mockFeatureManager();

        $cache->expects(self::once())
            ->method('contains')
            ->willReturn(true);

        $cache->expects(self::once())
            ->method('fetch')
            ->willReturn([]);

        $cache->expects(self::never())
            ->method('save');

        $delegatedManager->expects(self::never())
            ->method('findFeatureByName');

        $manager = new FeatureManager($cache, $delegatedManager);

        for($x=1;$x<=$count;$x++) {
            $feature = $manager->findFeatureByName('foo');

            self::assertInstanceOf('Dwo\Flagging\Model\FeatureInterface', $feature);
            self::assertEquals('foo', $feature->getName());
        }
    }

    public function providerCount()
    {
        return array(
            array(1),
            array(2),
        );
    }

    public function testCacheReadNotFound()
    {
        $cache = $this->mockCache();
        $delegatedManager = $this->mockFeatureManager();

        $cache->expects(self::once())
            ->method('contains')
            ->willReturn(false);

        $cache->expects(self::never())
            ->method('fetch');

        $cache->expects(self::never())
            ->method('save');

        $delegatedManager->expects(self::once())
            ->method('findFeatureByName')
            ->with('foo')
            ->willReturn(null);

        $manager = new FeatureManager($cache, $delegatedManager);
        $result = $manager->findFeatureByName('foo');

        self::assertNull($result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FeatureManagerInterface
     */
    protected function mockFeatureManager()
    {
        return $this->getMockBuilder('Dwo\Flagging\Model\FeatureManagerInterface')->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Cache
     */
    protected function mockCache()
    {
        return $this->getMockBuilder('Doctrine\Common\Cache\Cache')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
