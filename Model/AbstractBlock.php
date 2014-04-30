<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\BlockBundle\Model\BlockInterface;

use Symfony\Cmf\Bundle\CoreBundle\Model\ChildInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishTimePeriodInterface;

/**
 * Base class for all blocks - connects to Sonata Blocks
 *
 * Parent handling: The BlockInterface defines a parent to link back to
 * a container block if there is one. getParent may only return BlockInterface
 * objects, while getParentObject may return any "parent" even if its not
 * in a block hierarchy.
 */
abstract class AbstractBlock implements
    BlockInterface,
    PublishableInterface,
    PublishTimePeriodInterface,
    ChildInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var object
     */
    protected $parentDocument;

    /**
     * @var int
     */
    protected $ttl = 86400;

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var boolean whether this content is publishable
     */
    protected $publishable = true;

    /**
     * @var \DateTime|null publication start time
     */
    protected $publishStartDate;

    /**
     * @var \DateTime|null publication end time
     */
    protected $publishEndDate;

    /**
     * If you want your block model to be translated it has to implement TranslatableInterface
     * this code is just here to make your life easier
     *
     * @var string
     */
    protected $locale;

    /**
     * @param string $src
     *
     * @return string
     */
    protected function dashify($src)
    {
        return preg_replace('/[\/\.]/', '-', $src);
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function setEnabled($enabled)
    {
        $this->setPublishable($enabled);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnabled()
    {
        return $this->isPublishable();
    }

    /**
     * {@inheritDoc}
     */
    public function setPosition($position)
    {
        // TODO: implement. https://github.com/symfony-cmf/BlockBundle/issues/150

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        $siblings = $this->getParentObject()->getChildren();

        return array_search($siblings->indexOf($this), $siblings->getKeys());
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function setPublishable($publishable)
    {
        return $this->publishable = (bool) $publishable;
    }

    /**
     * {@inheritDoc}
     */
    public function isPublishable()
    {
        return $this->publishable;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * {@inheritDoc}
     */
    public function setPublishStartDate(\DateTime $publishStartDate = null)
    {
        $this->publishStartDate = $publishStartDate;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * {@inheritDoc}
     */
    public function setPublishEndDate(\DateTime $publishEndDate = null)
    {
        $this->publishEndDate = $publishEndDate;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addChildren(BlockInterface $children)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set parent object regardless of its type. This can be a ContainerBlock
     * but also any other object.
     *
     * {@inheritDoc}
     */
    public function setParentObject($parent)
    {
        $this->parentDocument = $parent;

        return $this;
    }

    /**
     * Get the parent object regardless of its type.
     *
     * {@inheritDoc}
     */
    public function getParentObject()
    {
        return $this->parentDocument;
    }

    /**
     * {@inheritDoc}
     *
     * Redirect to setParentObject
     */
    public function setParent(BlockInterface $parent = null)
    {
        return $this->setParentObject($parent);
    }

    /**
     * {@inheritDoc}
     *
     * Check if getParentObject is instanceof BlockInterface, otherwise return null
     */
    public function getParent()
    {
        if ($parent = $this->getParentObject() instanceof BlockInterface) {
            return $parent;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasParent()
    {
        return ($this->getParentObject() instanceof BlockInterface);
    }

    /**
     * Set ttl
     *
     * @param integer $ttl
     *
     * @return $this
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * toString ...
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setSettings(array $settings = array())
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * {@inheritDoc}
     */
    public function setSetting($name, $value)
    {
        $this->settings[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSetting($name, $default = null)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : $default;
    }

    /**
     * @return string
     */
    public function getDashifiedId()
    {
        return $this->dashify($this->id);
    }

    /**
     * @return string
     */
    public function getDashifiedType()
    {
        return $this->dashify($this->getType());
    }

    /**
     * If you want your block model to be translated it has to implement
     * TranslatableInterface. This code is just here to make your life easier.
     *
     * @see TranslatableInterface::getLocale()
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * If you want your block model to be translated it has to implement
     * TranslatableInterface. This code is just here to make your life easier.
     *
     * @see TranslatableInterface::setLocale()
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
