<?php

namespace Dwo\FlaggingBundle\Command;

use Dwo\Flagging\Serializer\FeatureSerializer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Dave Www <davewwwo@gmail.com>
 */
class FeaturesAllCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('dwo_flagging:features')
            ->addArgument('feature', InputArgument::OPTIONAL)
            ->setDescription('show all features');
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('dwo_flagging.manager.feature');

        $feature = $input->getArgument('feature');

        if ($feature) {
            $feature = $manager->findFeatureByName($feature);
            td($feature ? FeatureSerializer::serialize($feature) : 'not found');
        } else {
            $all = $manager->findAllFeatures();
            foreach ($all as $feature) {
                tde($feature->getName().' = '.Yaml::dump(FeatureSerializer::serialize($feature)));
            }
        }
    }
}
