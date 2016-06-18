<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
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
     * @return string
     */
    public function embedBlocks($text)
    {
        return $this->parse($text);
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
     * @param $name
     *
     * @return string
     */
    protected function embeddedRender($name)
    {
        try {
            return $this->render(array('name' => $name));
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->warn('Failed to render block "'.$name.'" embedded in content: '.$e->getTraceAsString());
            }
        }

        return '';
    }

    /**
     * @param $text
     *
     * @return string
     */
    protected function parse($text)
    {
        $segments = $this->segmentize($text);
        foreach ($segments as &$segment) {
            if (!is_array($segment)) {
                continue;
            }

            $segment[0] = $this->embeddedRender($segment[0]);
            $segment = implode('', $segment);
        }

        return implode('', $segments);
    }

    /**
     * @param string $text
     *
     * @return array
     */
    protected function segmentize($text)
    {
        $segments = explode($this->prefix, $text);
        foreach ($segments as $index => &$segment) {
            if ($index == 0) {
                continue;
            }

            if (strpos($segment, $this->postfix) !== false) {
                $segment = array_filter(explode($this->postfix, $segment));
                $segment[0] = trim($segment[0]);
            }
        }

        return $segments;
    }
}
