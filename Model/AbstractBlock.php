<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Model;

use Sonata\BlockBundle\Model\BlockInterface;

use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishTimePeriodInterface;

/**
 * Base class for all blocks - connects to Sonata Blocks
 *
 * Parent handling: The BlockInterface defines a parent to link back to
 * a container block if there is one. PHPCR-ODM blocks always have a parent
 * *document*. If the parent document is a BlockInterface, it is considered
 * a parent in the sonata sense as well.
 */
abstract class AbstractBlock implements
    BlockInterface,
    PublishableInterface,
    PublishTimePeriodInterface
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
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        $siblings = $this->getParent()->getChildren();

        return array_search($siblings->indexOf($this), $siblings->getKeys());
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
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
        return null;
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
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set parent document regardless of type. This can be a ContainerBlock
     * but also any PHPCR-ODM document.
     *
     * @param object $parent
     */
    public function setParentDocument($parent)
    {
        $this->parentDocument = $parent;
    }

    /**
     * Get the parent document regardless of its type.
     *
     * @return object|null $document
     */
    public function getParentDocument()
    {
        return $this->parentDocument;
    }

    /**
     * {@inheritDoc}
     *
     * Redirect to setParentDocument
     */
    public function setParent(BlockInterface $parent = null)
    {
        $this->setParentDocument($parent);
    }

    /**
     * {@inheritDoc}
     *
     * Check if parentDocument is instanceof BlockInterface, otherwise return null
     */
    public function getParent()
    {
        if ($this->parentDocument instanceof BlockInterface) {
            return $this->parentDocument;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasParent()
    {
        return ($this->parentDocument instanceof BlockInterface);
    }

    /**
     * Set ttl
     *
     * @param integer $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
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
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
