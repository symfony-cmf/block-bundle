<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that renders an action
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ActionBlock extends BaseBlock
{

    /** @PHPCRODM\String */
    protected $actionName;

    public function getType()
    {
        return 'symfony_cmf.block.action';
    }

    public function getActionName()
    {
        return $this->actionName;
    }

    public function setActionName($actionName)
    {
        return $this->actionName = $actionName;
    }

    /**
     * Set settings
     *
     * @param array $settings
     */
    public function setSettings(array $settings = array())
    {
        $this->settings = array_merge($this->getDefaultSettings(), $settings);
    }
    /**
     * @PHPCRODM\PrePersist
     */
    public function mergeDefaults()
    {
        // defaults
        if (is_null($this->actionName)) {
            $this->actionName = $this->getDefaultActionName();
        }

        if (count($this->settings) === 0) {
            $this->settings = array_merge($this->getDefaultSettings(), $this->getSettings());
        }
    }

    /**
     * Overload this method to define a default action name
     *
     * @return string|null
     */
    public function getDefaultActionName()
    {
        return null;
    }

    /**
     * Overload this method to define default settings
     *
     * @return array
     */
    public function getDefaultSettings()
    {
        return array();
    }
}
