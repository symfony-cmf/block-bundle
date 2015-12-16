<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Model;

use Eko\FeedBundle\Item\Reader\ItemInterface;

class FeedItem implements ItemInterface
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $link;

    /**
     * @var \DateTime
     */
    private $pubDate;

    /**
     * This method sets feed item title.
     *
     * @param string $title
     */
    public function setFeedItemTitle($title)
    {
        $this->title = $title;
    }

    /**
     * This method sets feed item description (or content).
     *
     * @param string $description
     */
    public function setFeedItemDescription($description)
    {
        $this->description = $description;
    }

    /**
     * This method sets feed item URL link.
     *
     * @param string $link
     */
    public function setFeedItemLink($link)
    {
        $this->link = $link;
    }

    /**
     * This method sets item publication date.
     *
     * @param \DateTime $date
     */
    public function setFeedItemPubDate(\DateTime $date)
    {
        $this->pubDate = $date;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return \DateTime
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }
}
