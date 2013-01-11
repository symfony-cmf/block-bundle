<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PHPCRBlockLoader implements BlockLoaderInterface
{
    protected $container;
    protected $documentManagerName;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param string $documentManagerName
     */
    public function __construct(ContainerInterface $container, $documentManagerName)
    {
        $this->container = $container;
        $this->dm = $this->container->get('doctrine_phpcr')->getManager($documentManagerName);
    }

    /**
     * {@inheritdoc}
     */
    public function load($configuration)
    {

        if ($this->support($configuration)) {
            $block = $this->findByName($configuration['name']);
            $block->setSettings($this->mergeSettings($block, $configuration));

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
     * @return BlockInterface
     */
    public function findByName($name)
    {
        if ($this->isAbsolutePath($name)) {
            return $this->dm->find(null, $name);
        }

        $currentPage = $this->container->get('request')->attributes->get('contentDocument');
        return $this->dm->find(null,  $currentPage->getPath() . '/' . $name);
    }

    /**
     * @param \string $path
     *
     * @return bool
     */
    protected function isAbsolutePath($path)
    {
        return substr($path, 0, 1) == '/';
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param array $configuration
     * @return array
     */
    private function mergeSettings(BlockInterface $block, $configuration)
    {
        return array_merge(
            isset($configuration['settings']) && is_array($configuration['settings']) ? $configuration['settings'] : array(),
            is_array($block->getSettings()) ? $block->getSettings() : array() // block document settings
        );
    }
}
