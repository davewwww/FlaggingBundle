<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Context\Context;
use Dwo\Flagging\Exception\FlaggingException;
use Dwo\Flagging\Model\VoterManagerInterface;
use Dwo\Flagging\Voter\VoterInterface;
use Dwo\FlaggingBundle\Tests\DependencyInjection;
use Symfony\Component\Yaml\Yaml;

class FiltersVotersTest extends AbstractContainerTestCase
{
    /**
     * @var VoterInterface
     */
    protected static $voter;

    /**
     * @beforeClass
     */
    public static function init()
    {
        self::createContainer();
        self::$voter = self::$container->get('dwo_flagging_voters.voter.filters');
    }

    /**
     * @dataProvider provider
     */
    public function test($result, $config)
    {
        self::assertEquals($result, self::$voter->vote($config, new Context()));
    }


    public function provider()
    {
        $enabledTrue = array('enabled' => [true]);
        $enabledFalse = array('enabled' => [false]);

        $config1 = array(
            $enabledFalse,
            array('filters' => array(
                $enabledTrue
            ))
        );

        $config2 = array(
            $enabledFalse,
            array('filters' => $config1)
        );

        $config3 = array(
            $enabledFalse,
            array('filters' => array(
                $enabledFalse,
                array('filters' => array(
                    $enabledFalse
                ))
            ))
        );

        return array(
            array(true, [$enabledTrue]),
            array(false, [$enabledFalse]),

            array(true, $config1),
            array(true, $config2),
            array(false, $config3),
        );
    }
}