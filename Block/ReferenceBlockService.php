<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockRendererInterface;

class ReferenceBlockService extends BaseBlockService implements BlockServiceInterface
{

    protected $blockRenderer;

    /**
     * @param string $name
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Sonata\BlockBundle\Block\BlockRendererInterface $blockRenderer
     */
    public function __construct($name, EngineInterface $templating, BlockRendererInterface $blockRenderer)
    {
        parent::__construct($name, $templating);
        $this->blockRenderer = $blockRenderer;
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
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param null|Response $response
     *
     * @return Response
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        // if the reference target block does not exist, we just skip the rendering
        if ($block->getEnabled() && null !== $block->getReferencedBlock()) {
            $response = $this->blockRenderer->render($block->getReferencedBlock());
        }

        return $response;
    }
}
