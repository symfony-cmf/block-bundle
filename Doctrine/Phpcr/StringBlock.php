<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

/**
 * Block that contains only text
 */
class StringBlock extends AbstractBlock implements TranslatableInterface
{
    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $locale;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cmf.block.string';
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
