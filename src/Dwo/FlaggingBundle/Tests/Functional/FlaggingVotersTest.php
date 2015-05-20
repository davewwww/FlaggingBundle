<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Context\Context;
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
     * @dataProvider providerVoters
     */
    public function testGetVoter($voterName, $null = false)
    {
        try {
            $voter = self::$manager->getVoter($voterName);
        } catch (FlaggingException $e) {
            $voter = null;
        }

        if ($null) {
            self::assertNull($voter);
        } else {
            self::assertNotNull($voter);
        }
    }

    /**
     * @dataProvider providerEnvironment
     */
    public function testEnvironmentVoter($result, $config)
    {
        $voter = self::$manager->getVoter('environment');

        self::assertEquals($result, $voter->vote($config, new Context()));
    }

    public function providerVoters()
    {
        return array(
            array('name'),
            array('expression'),
            array('date_range'),
            array('random'),
            array('feature'),
            array('enabled'),
            array('environment'),
            array('foo', true),
        );
    }

    public function providerEnvironment()
    {
        return array(
            array(true, 'test'),
            array(true, ['test']),
            array(true, ['prod', 'test']),
            array(false, 'prod'),
            array(false, ['prod', 'dev']),
        );
    }
}