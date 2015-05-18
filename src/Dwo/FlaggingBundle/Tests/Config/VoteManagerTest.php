<?php

namespace Dwo\FlaggingBundle\Tests\Config;

use Dwo\Flagging\Model\Feature;
use Dwo\Flagging\Model\FilterCollectionInterface;
use Dwo\Flagging\Model\FilterInterface;
use Dwo\Flagging\Tests\Fixtures\NameVoter;
use Dwo\FlaggingBundle\Config\FeatureManager;
use Dwo\FlaggingBundle\Config\VoterManager;

class VoteManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetVoter()
    {
        $manager = new VoterManager(array('name' => new NameVoter()));
        $voter = $manager->getVoter('name');

        self::assertInstanceOf('Dwo\Flagging\Voter\VoterInterface', $voter);
    }

    /**
     * @expectedException \Dwo\Flagging\Exception\FlaggingException
     */
    public function testVoterNotFound()
    {
        $manager = new VoterManager(array());
        $manager->getVoter('name');
    }

}
