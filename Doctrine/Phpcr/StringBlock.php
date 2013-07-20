<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

/**
 * Block that contains only text
 */
class StringBlock extends AbstractBlock
{
    /**
     * @var string
     */
    protected $body;

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

