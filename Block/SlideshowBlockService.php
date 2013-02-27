<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\BlockBundle\Block\ContainerBlockService;

class SlideshowBlockService extends ContainerBlockService
{

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return 'SymfonyCmfBlockBundle:Block:block_slideshow.html.twig';
    }

    /**
     * @param BlockInterface $block
     * @param null|Response $response
     *
     * @return Response
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if ($block->getEnabled()) {
            $childBlocks = array();
            foreach ($block->getChildren()->getValues() as $childBlock) {
                $expectedType = 'Symfony\Cmf\Bundle\BlockBundle\Document\SlideshowItemBlock';
                if (!$childBlock instanceof $expectedType) {
                    throw new \RuntimeException(sprintf('Expected block of type %s. Received block of type %s instead.', $expectedType, get_class($childBlock)));
                }
                $childBlocks[] =  $childBlock;
            }

            if (!empty($childBlock)) {
                return $this->renderResponse($this->getTemplate(), array(
                    'block' => $block,
                    'itemBlocks' => $childBlocks,
                    'settings'    => $this->getDefaultSettings()
                ), $response);
            }

        }

        return $response;
    }

}
