<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Templating\Helper;

/**
 * Finds all the embedded blocks.
 *
 * @author Viorel Craescu <viorel@craescu.com>
 */
class EmbedBlocksParser
{
    private $prefix;
    private $postfix;

    /**
     * EmbedBlocksParser constructor.
     *
     * @param string $prefix
     * @param string $postfix
     */
    public function __construct($prefix, $postfix)
    {
        $this->prefix = $prefix;
        $this->postfix = $postfix;
    }

    /**
     * @param string   $text
     * @param callable $callback
     *
     * @return string
     */
    public function parse($text, callable $callback)
    {
        $segments = $this->segmentize($text);
        foreach ($segments as &$segment) {
            if (!is_array($segment)) {
                continue;
            }

            $segment[0] = $callback($segment[0]);
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
            if (0 === $index) {
                continue;
            }

            if (false !== strpos($segment, $this->postfix)) {
                $segment = array_filter(explode($this->postfix, $segment));
                $segment[0] = trim($segment[0]);
            }
        }

        return $segments;
    }
}
