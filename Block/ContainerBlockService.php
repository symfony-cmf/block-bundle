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

class ContainerBlockService extends BaseBlockService implements BlockServiceInterface
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
        // Not used at the moment, editing using a frontend or backend UI could be changed here
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // Not used at the moment, validation for editing using a frontend or backend UI could be changed here
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return 'SymfonyCmfBlockBundle:Block:block_container.html.twig';
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if ($block->getEnabled()) {
            // merge settings
            $settings = is_array($block->getSettings()) ? array_merge($this->getDefaultSettings(), $block->getSettings()) : $this->getDefaultSettings();

            $childBlocks = array();
            foreach ($block->getChildren()->getValues() as $childBlock) {
                $childBlocks[] =  $this->blockRenderer->render($childBlock)->getContent();
            }

            return $this->renderResponse($this->getTemplate(), array(
                'childBlocks' => $childBlocks,
                'settings'    => $settings
            ), $response);
        }

        return $response;
    }

    /**
     * Returns the default settings link to the service
     *
     * @return array
     */
    public function getDefaultSettings()
    {
        return array(
            'divisibleBy'    => false,
            'divisibleClass' => '',
            'childClass'     => '',
        );
    }
}
