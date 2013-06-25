<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Sonata\BlockBundle\Twig\Extension\BlockExtension;
use Sonata\BlockBundle\Exception\BlockNotFoundException;

/**
 * Helper functions for blocks.
 *
 * @author Wouter J <waldio.webdesign@gmail.com>
 */
class CmfBlockHelper extends Helper
{
    /**
     * @var BlockExtension
     */
    private $sonataBlock;

    private $prefix;

    private $postfix;

    /**
     * @var LoggerInterface
     */
    private $logger;

    function __construct(BlockExtension $sonataBlock, $prefix, $postfix, LoggerInterface $logger = null)
    {
        $this->sonataBlock = $sonataBlock;
        $this->prefix = $prefix;
        $this->postfix = $postfix;
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
        // with the default prefix and postfix, this will do <span>block:"block-identifier"</span>
        return preg_replace_callback('#' . $this->prefix . '"([^\"]+)"' . $this->postfix . '#', array($this, 'renderBlock'), $text);
    }

    /**
     * Executes the block as specified in the content.
     *
     * @param array $block An array including the block name
     *
     * @return string the rendered block
     */
    public function renderBlock($block)
    {
        try {
            return $this->sonataBlock->renderBlock(array('name' => $block[1]));
        } catch (BlockNotFoundException $e) {
            if ($this->logger) {
                $this->logger->warn('Failed to render block "' . $block[1] . '" embedded in content: ' . $e->getTraceAsString());
            }
        }
        return '';
    }

    public function getName()
    {
        return 'cmf_block';
    }
}
