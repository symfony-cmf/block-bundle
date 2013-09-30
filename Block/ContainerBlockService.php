<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContainerBlockService extends BaseBlockService implements BlockServiceInterface
{

    protected $blockRenderer;
    protected $template = 'CmfBlockBundle:Block:block_container.html.twig';

    /**
     * @param string                                                     $name
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Sonata\BlockBundle\Block\BlockRendererInterface           $blockRenderer
     * @param string|null                                                $template
     */
    public function __construct($name, EngineInterface $templating, BlockRendererInterface $blockRenderer, $template = null)
    {
        parent::__construct($name, $templating);

        $this->blockRenderer = $blockRenderer;

        if ($template) {
            $this->template = $template;
        }
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
        if (!$response) {
            $response = new Response();
        }

        if ($blockContext->getBlock()->getEnabled()) {
            return $this->renderResponse($blockContext->getTemplate(), array(
                'block'       => $blockContext->getBlock(),
                'settings'    => $blockContext->getSettings(),
            ), $response);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template'       => $this->template,
            'divisible_by'    => 0,
            'divisible_class' => '',
            'child_class'     => '',
        ));

        $resolver->addAllowedTypes(array(
            'divisible_by'    => array('integer'),
            'divisible_class' => array('string'),
            'child_class'     => array('string'),
        ));
    }
}
