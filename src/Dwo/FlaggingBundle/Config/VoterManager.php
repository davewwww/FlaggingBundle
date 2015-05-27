<?php

namespace Dwo\FlaggingBundle\Config;

use Dwo\Flagging\Exception\FlaggingException;
use Dwo\Flagging\Model\VoterManagerInterface;
use Dwo\Flagging\Voter\VoterInterface;

/**
 * @author David Wolter <david@lovoo.com>
 */
class VoterManager implements VoterManagerInterface
{
    /**
     * @var VoterInterface[]
     */
    protected $voters;

    /**
     * @param array $voters
     */
    public function __construct(array $voters = array())
    {
        $this->voters = $voters;
    }

    /**
     * @param string $name
     *
     * @throws FlaggingException
     *
     * @return VoterInterface
     */
    public function getVoter($name)
    {
        if (!isset($this->voters[$name])) {
            throw new FlaggingException(sprintf('voter "%s" not found', $name));
        }

        return $this->voters[$name];
    }

    /**
     * @return VoterInterface[]
     */
    public function getAllVoters()
    {
        return $this->voters;
    }
}
