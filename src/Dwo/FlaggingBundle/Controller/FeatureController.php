<?php

namespace Dwo\FlaggingBundle\Controller;

use Dwo\Flagging\Model\FeatureManagerInterface;
use Dwo\Flagging\Serializer\FeatureSerializer;
use Dwo\FlaggingBundle\Model\Form\Feature;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

/**
 * Class FeatureController
 *
 * @author Dave Www <davewwwo@gmail.com>
 */
class FeatureController extends ContainerAware
{
    /**
     * @param string $featureName
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function readAction($featureName)
    {
        $managerName = 'dwo_flagging.manager.feature.db_and_config';
        if($this->getRequest()->query->has('live')) {
            $managerName = 'dwo_flagging.manager.feature';
        }

        /** @var FeatureManagerInterface $manager */
        $manager = $this->container->get($managerName);
        $feature = $manager->findFeatureByName($featureName);

        if (null === $feature) {
            throw new \Exception(sprintf('Feature "%s" not found', $featureName));
        }

        $featureArray = FeatureSerializer::serialize($feature);
        $featureYaml = Yaml::dump($featureArray, 3, 2);

        $template = file_get_contents(__DIR__.'/../Resources/view/index.html');
        $template = str_replace(array('{NAME}', '{DATA}'), array($featureName, $featureYaml), $template);

        return new Response($template);
    }

    /**
     * @param string $featureName
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function writeAction($featureName)
    {
        $dataYaml = $this->getRequest()->request->get('data');

        $form = new Feature();
        $form->setName($featureName);
        $form->setContent($dataArray = Yaml::parse($dataYaml));

        $validator = $this->container->get('validator');
        $violations = $validator->validate($form);
        if ($violations->has(0)) {
            throw new \Exception($violations->get(0));
        }

        $featureHandler = $this->container->get('dwo_flagging.handler.feature');
        $featureHandler->saveFeature($featureName, $dataArray);

        return new Response('ok<p><a href="?">weiter</a>');
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

}