<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Model\VoterManagerInterface;
use Dwo\FlaggingBundle\Tests\DependencyInjection;

class VoterManagerTest extends AbstractContainerTestCase
{
    /**
     * @var VoterManagerInterface
     */
    protected static $manager;

    /**
     * @beforeClass
     */
    public static function init()
    {
        self::createContainer();
        self::$manager = self::$container->get('dwo_flagging.manager.voter');
    }

    public function testGetVoter()
    {
        $voter = self::$manager->getVoter('name');
        self::assertNotNull($voter);
    }
}