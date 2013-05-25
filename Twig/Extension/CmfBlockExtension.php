<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Sonata\BlockBundle\Twig\Extension\BlockExtension;
use Sonata\BlockBundle\Exception\BlockNotFoundException;

/**
 * Utility function for blocks
 *
 * @author David Buchmann <david@liip.ch>
 */
class CmfBlockExtension extends \Twig_Extension
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

    public function getFilters()
    {
        return array(
            'cmf_embed_blocks' => new \Twig_Filter_Method($this, 'cmfEmbedBlocks', array('is_safe' => array('html'))),
        );
    }

    /**
     * Implement the cmf_embed_blocks filter, looking for special markers that
     * identify blocks and replacing them with the result of rendering the
     * specified identifier.
     *
     * @param string $text
     *
     * @return mixed
     */
    public function cmfEmbedBlocks($text)
    {
        // with the default prefix and postfix, this will do <span>block:"block-identifier"</span>
        return preg_replace_callback('#' . $this->prefix . '"([^\"]+)"' . $this->postfix . '#', array($this, 'renderBlock'), $text);
    }

    /**
     * Execute the block as specified in the content.
     *
     * @param string $block the block name
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