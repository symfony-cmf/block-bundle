<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class ActionBlockService extends BaseBlockService
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var FragmentHandler
     */
    protected $renderer;

    /**
     * @param string          $name
     * @param EngineInterface $templating
     * @param FragmentHandler $renderer
     */
    public function __construct($name, EngineInterface $templating, FragmentHandler $renderer)
    {
        parent::__construct($name, $templating);
        $this->renderer = $renderer;
    }

    /**
     * Set the request.
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        throw new \RuntimeException('Not used at the moment, editing using a frontend or backend UI could be changed here');
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        throw new \RuntimeException('Not used at the moment, validation for editing using a frontend or backend UI could be changed here');
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
            return new Response;
        }

        $requestParams = $block->resolveRequestParams($this->request, $blockContext);

        return new Response($this->renderer->render(new ControllerReference(
                $block->getActionName(),
                $requestParams
            )
        ));
    }
}
