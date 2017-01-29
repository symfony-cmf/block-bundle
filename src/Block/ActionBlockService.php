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
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class ActionBlockService extends AbstractBlockService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var FragmentHandler
     */
    protected $renderer;

    /**
     * @param string          $name
     * @param EngineInterface $templating
     * @param FragmentHandler $renderer
     */
    public function __construct(RequestStack $requestStack, $name, EngineInterface $templating, FragmentHandler $renderer)
    {
        parent::__construct($name, $templating);
        $this->renderer = $renderer;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        /** @var $block ActionBlock */
        $block = $blockContext->getBlock();

        if (!$block->getActionName()) {
            throw new \RuntimeException(sprintf(
                'ActionBlock with id "%s" does not have an action name defined, implement a default or persist it in the document.',
                $block->getId()
            ));
        }

        if (!$block->getEnabled()) {
            return new Response();
        }

        $requestParams = $block->resolveRequestParams($this->requestStack->getCurrentRequest(), $blockContext);

        return new Response($this->renderer->render(new ControllerReference(
                $block->getActionName(),
                $requestParams
            )
        ));
    }
}
