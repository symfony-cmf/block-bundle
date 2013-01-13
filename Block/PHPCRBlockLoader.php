<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockServiceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PHPCRBlockLoader implements BlockLoaderInterface
{
    protected $container;
    protected $documentManagerName;

    /**
     * @var BlockServiceManagerInterface
     */
    private $blockServiceManager;

    /**
     * @var OptionsResolverInterface[]
     */
    private $optionsResolvers;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param string $documentManagerName
     */
    public function __construct(ContainerInterface $container, $documentManagerName, BlockServiceManagerInterface $blockServiceManager)
    {
        $this->container = $container;
        $this->dm = $this->container->get('doctrine_phpcr')->getManager($documentManagerName);
        $this->blockServiceManager = $blockServiceManager;
    }

    /**
     * @return \Sonata\BlockBundle\Block\BlockServiceManagerInterface
     */
    public function getBlockServiceManager()
    {
        return $this->blockServiceManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load($configuration)
    {
        if ($this->support($configuration)) {
            $block = $this->findByName($configuration['name']);
            unset($configuration['name']);

            $this->createResolvedBlock($block, $configuration);

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
     * Returns the configured options resolver used for this block.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolverInterface The options resolver.
     */
    public function getOptionsResolver(BlockInterface $block)
    {
        if (!isset($this->optionsResolvers[$block->getType()])) {
            $this->optionsResolvers[$block->getType()] = new OptionsResolver();

            $blockService = $this->getBlockServiceManager()->get($block);
            $blockService->setDefaultOptions($this->optionsResolvers[$block->getType()]);
        }

        return $this->optionsResolvers[$block->getType()];
    }

    /**
     * Resolves a block
     *
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param array $configuration
     */
    public function createResolvedBlock(BlockInterface $block, array $configuration = array())
    {
        $settings = isset($configuration['settings']) ? $configuration['settings'] : array();
        $options = $configuration;
        $childrenConfiguration = isset($configuration['children']) ? $configuration['children'] : array();
        unset($options['settings']);
        unset($options['children']);

        // merge settings
        $block->setSettings(array_merge(
            $settings,
            is_array($block->getSettings()) ? $block->getSettings() : array()
        ));

        // resolve options
        $resolver = $this->getOptionsResolver($block);
        $block->setOptions($resolver->resolve($options));

        // resolve children
        if ($block->hasChildren()) {
            foreach ($block->getChildren() as $childBlock) {
                $childConfiguration = isset($childrenConfiguration[$childBlock->getType()]) ? $childrenConfiguration[$childBlock->getType()] : array();

                $this->createResolvedBlock($childBlock, $childConfiguration);
            }
        }
    }
}
