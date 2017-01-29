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
use Sonata\BlockBundle\Block\Service\BlockServiceInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The menu block service renders the template with the specified menu node.
 *
 * @author Philipp A. Mohrenweiser <phiamo@googlemail.com>
 */
class MenuBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $template = 'CmfBlockBundle:Block:block_menu.html.twig';

    public function __construct($name, $templating, $template = null)
    {
        parent::__construct($name, $templating);

        if ($template) {
            $this->template = $template;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        // if the referenced target menu does not exist, we just skip the rendering
        if (!$block->getEnabled() || null === $block->getMenuNode()) {
            return $response ?: new Response();
        }

        $menuNode = $block->getMenuNode();

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'menu' => $menuNode->getId(),
                'block' => $blockContext->getBlock(),
            ),
            $response
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $this->configureSettings($resolver);
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template' => $this->template,
        ));
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
