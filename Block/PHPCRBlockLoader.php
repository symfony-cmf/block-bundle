<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Model\EmptyBlock;
use Sonata\BlockBundle\Exception\BlockNotFoundException;

/**
 * The PHPCR block loader loads from phpcr-odm, both by absolute path and
 * by path relative to the contentDocument in the request attributes.
 *
 * It can be configured to return an EmptyBlock in case no block is found at
 * the specified location.
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
     * @var \Doctrine\ODM\PHPCR\DocumentManager
     */
    protected $dm;

    /**
     * @var string service id of the empty block service
     */
    protected $emptyBlockType;

    /**
     * @param ContainerInterface $container
     * @param string $documentManagerName
     * @param LoggerInterface $logger
     * @param null $emptyBlockType set this to a block type name if you want empty blocks returned when no block is found
     */
    public function __construct(ContainerInterface $container, $documentManagerName, LoggerInterface $logger = null, $emptyBlockType = null)
    {
        $this->container       = $container;
        $this->dm              = $this->container->get('doctrine_phpcr')->getManager($documentManagerName);
        $this->logger          = $logger;
        $this->emptyBlockType  = $emptyBlockType;
    }

    /**
     * {@inheritdoc}
     */
    public function load($configuration)
    {
        if (! $this->support($configuration)) {
            // sanity check, the chain loader should already have checked.
            throw new BlockNotFoundException('A block is tried to be loaded with an unsupported configuration');
        }

        $block = $this->findByName($configuration['name']);
        if (! $block instanceof BlockInterface) {
            // not found or no valid block
            return $this->getNotFoundBlock($configuration['name'], sprintf(
                "Document at '%s' is no Sonata\\BlockBundle\\Model\\BlockInterface but %s",
                $configuration['name'],
                is_null($block) ? 'not existing' : get_class($block)
            ));
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
     * Finds one block by the given name (PHPCR path)
     *
     * @param string $name a relative or absolute PHPCR path
     *
     * @return BlockInterface|null the block at that location or null if no document or not a BlockInterface at that location
     */
    protected function findByName($name)
    {
        $path = $this->determineAbsolutePath($name);

        if (null == $path) {
            if ($this->logger) {
                $this->logger->debug("Block '$name' is not an absolute path and there is no 'contentDocument' in the request attributes");
            }

            return null;
        }

        $block = $this->dm->find(null, $path);

        if (empty($block) && $this->logger) {
            $this->logger->debug("Block '$name' at path '$path' could not be found.");
        }

        return $block;
    }

    /**
     * Check if $path is absolute or not
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isAbsolutePath($path)
    {
        return is_string($path)
            && strlen($path) > 0
            && $path[0] == '/'
        ;
    }

    /**
     * Find the absolute path from this name. If $name is relative, prepend the
     * path to the contentDocument in the request attributes if it exists.
     *
     * @param string $name
     *
     * @return string|null absolute PHPCR path if possible, null if not determined
     */
    protected function determineAbsolutePath($name)
    {
        if ($this->isAbsolutePath($name)) {
            return $name;
        }

        if ($this->container->has('request')
            && $this->container->get('request')->attributes->has('contentDocument')
        ) {
            $currentPage = $this->container->get('request')->attributes->get('contentDocument');
            return $this->dm->getUnitOfWork()->getDocumentId($currentPage) . '/' . $name;
        }

        return null;
    }

    /**
     * Get the block to return when a block is not found or the thing found was
     * no BlockInterface.
     *
     * If the empty block type is not set, throw an exception instead.
     *
     * @param string $name The block name that was not found or invalid
     * @param string $message The exception message if an exception should be raised
     *
     * @return EmptyBlock
     *
     * @throws BlockNotFoundException if there is no type defined for the empty block.
     */
    private function getNotFoundBlock($name, $message = null)
    {
        if (is_null($this->getEmptyBlockType())) {
            throw new BlockNotFoundException($message);
        }

        $block = new EmptyBlock();
        $block->setType($this->getEmptyBlockType());
        $block->setUpdatedAt(new \DateTime());

        $path = $this->determineAbsolutePath($name);
        if (! is_null($path)) {
            $block->setId($path);
        }

        return $block;
    }

    /**
     * @return string|null service id of the empty block service, null if not set
     */
    public function getEmptyBlockType()
    {
        return $this->emptyBlockType;
    }

    /**
     * @param string $type service id of the empty block service
     */
    public function setEmptyBlockType($type = null)
    {
        $this->emptyBlockType = $type;
    }
}
