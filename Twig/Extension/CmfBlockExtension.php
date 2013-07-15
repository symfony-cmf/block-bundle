<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Sonata\BlockBundle\Twig\Extension\BlockExtension;
use Sonata\BlockBundle\Exception\BlockNotFoundException;

/**
 * Utility function for blocks
 *
 * @author David Buchmann <david@liip.ch>
 */
class CmfBlockExtension extends CmfBlockHelper implements \Twig_ExtensionInterface
{
    public function getFilters()
    {
        return array(
            'cmf_embed_blocks' => new \Twig_Filter_Method($this, 'cmfEmbedBlocks', array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return 'cmf_block';
    }

    // from \Twig_Extension

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(\Twig_Environment $environment)
    {
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array();
    }

    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return array An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return array();
    }

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return array An array of tests
     */
    public function getTests()
    {
        return array();
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array();
    }

    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators()
    {
        return array();
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return array();
    }
}
