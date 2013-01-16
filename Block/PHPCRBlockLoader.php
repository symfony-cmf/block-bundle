<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\BlockInterface;

/**
 * The PHPCR block loader loads from phpcr-odm, both by absolute path and
 * by path relative to the contentDocument in the request attributes.
 */
class PHPCRBlockLoader implements BlockLoaderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var null|LoggerInterface
     */
    protected $logger;


    /**
     * @param ContainerInterface $container
     * @param string $documentManagerName
     */
    public function __construct(ContainerInterface $container, $documentManagerName, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->dm = $this->container->get('doctrine_phpcr')->getManager($documentManagerName);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function load($configuration)
    {
        if ($this->support($configuration)) {
            $block = $this->findByName($configuration['name']);
            if (null === $block) {
                // not found or no valid block
                return null;
            }

            // merge settings
            $userSettings = isset($configuration['settings']) && is_array($configuration['settings']) ?
                $configuration['settings'] :
                array()
            ;
            $defaultSettings = is_array($block->getSettings()) ? $block->getSettings() : array();
            $block->setSettings(array_merge($userSettings, $defaultSettings));

            return $block;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function support($configuration)
    {
        if (!is_array($configuration)) {
            return false;
        }

        if (!isset($configuration['name'])) {
            return false;
        }

        return true;
    }

    /**
     * Finds one block by the given name
     *
     * @param string $name
     *
     * @return BlockInterface|null the block or null if not found/no BlockInterface at that location
     */
    protected function findByName($name)
    {
        if ($this->isAbsolutePath($name)) {
            $block = $this->dm->find(null, $name);
        } else if ($this->container->has('request')
            && $this->container->get('request')->attributes->has('contentDocument')
        ) {
            $currentPage = $this->container->get('request')->attributes->get('contentDocument');
            $block = $this->dm->find(null,  $currentPage->getPath() . '/' . $name);
        } else {
            if ($this->logger) {
                $this->logger->debug("Block '$name' is not absolute path and either there is no contentDocument or the relative path does not match");
            }

            return null;
        }

        if (! $block instanceof BlockInterface) {
            if ($this->logger) {
                $this->logger->debug("Document at '$name' is no Sonata\\BlockBundle\\Model\\BlockInterface but " . get_class($block));
            }

            return null;
        }

        return $block;
    }

    /**
     * @param \string $path
     *
     * @return bool
     */
    protected function isAbsolutePath($path)
    {
        return is_string($path)
            && strlen($path) > 0
            && substr($path, 0, 1) == '/'
        ;
    }
}
