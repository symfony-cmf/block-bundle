<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Controller;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Cmf\Bundle\BlockBundle\Model\FeedItem;
use Zend\Feed\Reader\Exception\RuntimeException;

class RssController extends Controller
{
    /**
     * Action that is referenced in an ActionBlock.
     *
     * @param BlockInterface        $block
     * @param BlockContextInterface $blockContext
     *
     * @return Response the response
     */
    public function block(BlockInterface $block, BlockContextInterface $blockContext)
    {
        return $this->render($blockContext->getTemplate(), array(
            'block' => $block,
            'items' => $this->getItems($blockContext),
            'settings' => $blockContext->getSettings(),
        ));
    }

    /**
     * Get items that the list block template can render,
     * use the settings from the block passed.
     *
     * @param BlockContextInterface $blockContext
     *
     * @return FeedItem[] feed items that the block template can render
     */
    protected function getItems(BlockContextInterface $blockContext)
    {
        $settings = $blockContext->getSettings();
        if (empty($settings['url']) || empty($settings['maxItems']) || empty($settings['itemClass'])) {
            $this->get('logger')->debug(sprintf(
                    'RssBlock with id "%s" is missing a required setting: url, maxItems, itemClass',
                    $blockContext->getBlock()->getId()
                ));

            return array();
        }

        if (!$this->has('eko_feed.feed.reader')) {
            throw new \RuntimeException('Service "eko_feed.feed.reader" not found, install the EkoFeedBundle.');
        }

        try {
            $reader = $this->get('eko_feed.feed.reader');
            $items = $reader->load($settings['url'])->populate($settings['itemClass']);
        } catch (RuntimeException $e) {
            // feed import failed
            $this->get('logger')->debug(sprintf(
                'RssBlock with id "%s" could not import feed from "%s", error: %s',
                $blockContext->getBlock()->getId(),
                    $settings['url'],
                $e->getMessage()
            ));
            $items = array();
        }

        return array_slice($items, 0, $settings['maxItems']);
    }
}
