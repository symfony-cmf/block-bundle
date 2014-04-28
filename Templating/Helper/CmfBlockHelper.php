<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Sonata\BlockBundle\Templating\Helper\BlockHelper as SonataBlockHelper;

/**
 * Helper functions for blocks.
 *
 * @author Wouter J <waldio.webdesign@gmail.com>
 */
class CmfBlockHelper extends Helper
{
    /**
     * @var SonataBlockHelper
     */
    private $sonataBlock;

    private $prefix;

    private $postfix;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(SonataBlockHelper $sonataBlock, $prefix, $postfix, LoggerInterface $logger = null)
    {
        $this->sonataBlock = $sonataBlock;
        $this->prefix = preg_quote($prefix, '#');
        $this->postfix = preg_quote($postfix, '#');
        $this->logger = $logger;
    }

    /**
     * Looks for special markers that identify blocks and replaces
     * them with the result of rendering the specified identifier.
     *
     * @param string $text
     *
     * @return mixed
     */
    public function embedBlocks($text)
    {
        // with the default prefix and postfix, this will do %embed-block|block-identifier|end%
        $endDelimiter = preg_quote($this->postfix[0], '#');

        return preg_replace_callback('#' . $this->prefix . '([^' . $endDelimiter .']+)' . $this->postfix . '#', array($this, 'embeddedRender'), $text);
    }

    /**
     * @see SonataBlockHelper::render
     */
    public function render($block, array $options = array())
    {
        return $this->sonataBlock->render($block, $options);
    }

    /**
     * @see SonataBlockHelper::includeJavascripts
     */
    public function includeJavascripts($media)
    {
        return $this->sonataBlock->includeJavaScripts($media);
    }

    /**
     * @see SonataBlockHelper::includeStylesheets
     */
    public function includeStylesheets($media)
    {
        return $this->sonataBlock->includeStylesheets($media);
    }

    public function getName()
    {
        return 'blocks';
    }

    /**
     * Executes the block as specified in the content.
     *
     * @param array $block An array including the block name
     *
     * @return string the rendered block
     */
    protected function embeddedRender($block)
    {
        try {
            return $this->sonataBlock->render(array('name' => trim($block[1])));
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->warn('Failed to render block "' . $block[1] . '" embedded in content: ' . $e->getTraceAsString());
            }
        }

        return '';
    }
}
