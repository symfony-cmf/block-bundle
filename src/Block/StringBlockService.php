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

class StringBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $template = 'CmfBlockBundle:Block:block_string.html.twig';

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
        if (!$response) {
            $response = new Response();
        }

        if ($blockContext->getBlock()->getEnabled()) {
            $response = $this->renderResponse($blockContext->getTemplate(), array('block' => $blockContext->getBlock()), $response);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $this->configureSettings($resolver);
    }

    /**
     * {@inheritdoc}
     */
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
