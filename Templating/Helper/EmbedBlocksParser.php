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
     * @param $prefix
     * @param $postfix
     */
    public function __construct($prefix, $postfix)
    {
        $this->prefix = $prefix;
        $this->postfix = $postfix;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     *
     * @return EmbedBlocksParser
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostfix()
    {
        return $this->postfix;
    }

    /**
     * @param mixed $postfix
     *
     * @return EmbedBlocksParser
     */
    public function setPostfix($postfix)
    {
        $this->postfix = $postfix;

        return $this;
    }

    /**
     * @param $text
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
     * @param $text
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
