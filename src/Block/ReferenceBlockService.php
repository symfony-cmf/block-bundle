<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class ReferenceBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $blockRenderer;
    protected $blockContextManager;

    /**
     * @param string                       $name
     * @param EngineInterface              $templating
     * @param BlockRendererInterface       $blockRenderer
     * @param BlockContextManagerInterface $blockContextManager
     */
    public function __construct($name, EngineInterface $templating, BlockRendererInterface $blockRenderer, BlockContextManagerInterface $blockContextManager)
    {
        parent::__construct($name, $templating);
        $this->blockRenderer = $blockRenderer;
        $this->blockContextManager = $blockContextManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        // if the reference target block does not exist, we just skip the rendering
        if ($blockContext->getBlock()->getEnabled() && null !== $blockContext->getBlock()->getReferencedBlock()) {
            $referencedBlockContext = $this->blockContextManager->get($blockContext->getBlock()->getReferencedBlock());

            $response = $this->blockRenderer->render($referencedBlockContext);
        }

        return $response;
    }
}
