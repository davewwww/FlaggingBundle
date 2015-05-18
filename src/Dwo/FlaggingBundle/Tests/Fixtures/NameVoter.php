<?php

namespace Dwo\FlaggingBundle\Tests\Fixtures;

use Dwo\Flagging\Context\Context;
use Dwo\Flagging\Voter\VoterInterface;
use Dwo\Flagging\Walker;

class NameVoter implements VoterInterface
{
    /**
     * @param mixed   $config
     * @param Context $context
     *
     * @return Boolean
     */
    public function vote($config, Context $context)
    {
        return Walker::walkOr(
            $config,
            function ($entry) use ($context) {
                return $context->getParam('name') === $entry;
            },
            true
        );
    }
}