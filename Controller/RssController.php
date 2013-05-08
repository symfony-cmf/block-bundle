<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Controller;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\BlockBundle\Model\FeedItem;

class RssController extends Controller
{
    /**
     * Action that is referenced in an ActionBlock
     *
     * @param BlockInterface $block
     *
     * @return Response the response
     */
    public function block(BlockInterface $block, BlockContextInterface $blockContext)
    {
        return $this->render($blockContext->getTemplate(), array(
            'block' => $block,
            'items' => $this->getItems($block)
        ));
    }

    /**
     * Get items that the list block template can render,
     * use the settings from the block passed
     *
     * @param BlockInterface
     * @return FeedItem[] feed items that the block template can render
     */
    protected function getItems(BlockInterface $block)
    {
        if ($block->getSetting('url', false)
            && $block->getSetting('maxItems', false)
            && $block->getSetting('itemClass', false)
        ) {
            if (!$this->has('eko_feed.feed.reader')) {
                throw new \RuntimeException('Service "eko_feed.feed.reader" not found, install the EkoFeedBundle.');
            }

            try {
                $reader = $this->get('eko_feed.feed.reader');
                $items = $reader->load($block->getSetting('url'))->populate($block->getSetting('itemClass'));
            } catch (\Zend\Feed\Reader\Exception\RuntimeException $e) {
                // feed import failed
                $this->get('logger')->debug(sprintf(
                    'RssBlock with id "%s" could not import feed from "%s", error: %s',
                    $block->getId(),
                    $block->getSetting('url'),
                    $e->getMessage()
                ));
                $items = array();
            }

            return array_slice($items, 0, $block->getSetting('maxItems'));
        } else {
            $this->get('logger')->debug(sprintf(
                'RssBlock with id "%s" is missing a required setting: url, maxItems, itemClass',
                $block->getId()
            ));

            return array();
        }
    }
}
