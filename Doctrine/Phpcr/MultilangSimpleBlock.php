<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

/**
 * Block that contains hypertext and a title
 */
class MultilangSimpleBlock extends AbstractBlock
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $body;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cmf.block.simple';
    }

    /**
     * Get locale used for this block
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale. When doing a flush, this will have the translated
     * fields be stored as that locale.
     *
     * @param string $locale the locale to use for this block
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
