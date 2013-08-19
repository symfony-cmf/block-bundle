<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Model;

use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

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
    PublishTimePeriodInterface,
    TranslatableInterface
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
    protected $locale = false;

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
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
    }

    /**
     * This is required by BlockInterface
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->setPublishable($enabled);
    }

    /**
     * This is required by BlockInterface
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->isPublishable();
    }

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position)
    {
        // TODO: implement
    }

    /**
     * Get position
     *
     * @return integer $position
     */
    public function getPosition()
    {
        $siblings = $this->getParent()->getChildren();

        return array_search($siblings->indexOf($this), $siblings->getKeys());
    }

    /**
     * Sets the creation date and time
     *
     * @param \Datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get creation date
     *
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the last update date and time
     *
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get update date
     *
     * @return \Datetime
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
     * Add children
     *
     * @param BlockInterface $children
     */
    public function addChildren(BlockInterface $children)
    {
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection $children
     */
    public function getChildren()
    {
        return null;
    }

    /**
     * @abstract
     * @return bool
     */
    public function hasChildren()
    {
        return false;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * set parent document regardless of type. this can be a ContainerBlock
     * but also any PHPCR-ODM document
     *
     * @param object $parent
     */
    public function setParentDocument($parent)
    {
        $this->parentDocument = $parent;
    }

    /**
     * get the parent document
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
     * Get ttl
     *
     * @return integer
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
     * Set settings
     *
     * @param array $settings
     */
    public function setSettings(array $settings = array())
    {
        $this->settings = $settings;
    }

    /**
     * Get settings
     *
     * @return array $settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function setSetting($name, $value)
    {
        $this->settings[$name] = $value;
    }

    /**
     * @param $name
     * @param  null $default
     * @return null
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
     * If you want your block model to be translated it has to implement TranslatableInterface
     * this code is just here to make your life easier
     *
     * @see TranslatableInterface::getLocale()
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * If you want your block model to be translated it has to implement TranslatableInterface
     * this code is just here to make your life easier
     *
     * @see TranslatableInterface::setLocale()
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
