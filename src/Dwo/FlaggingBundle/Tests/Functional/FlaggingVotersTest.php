<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Exception\FlaggingException;
use Dwo\Flagging\Model\VoterManagerInterface;
use Dwo\FlaggingBundle\Tests\DependencyInjection;

class FlaggingVotersTest extends AbstractContainerTestCase
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

    /**
     * @dataProvider provider
     */
    public function testGetVoter($voterName, $null = false)
    {
        try {
            $voter = self::$manager->getVoter($voterName);
        }
        catch(FlaggingException $e) {
            $voter = null;
        }

        if($null) {
            self::assertNull($voter);
        }
        else {
            self::assertNotNull($voter);
        }
    }

    public function provider() {
        return array(
            array('name'),
            array('expression'),
            array('date_range'),
            array('random'),
            array('feature'),
            array('foo', true),
        );
    }
}