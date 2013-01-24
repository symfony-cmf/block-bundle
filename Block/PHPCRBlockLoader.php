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
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param $documentManagerName
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     * @param null $emptyBlockType
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
        if ($this->support($configuration)) {
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

        throw new BlockNotFoundException('A block is tried to be loaded with an unsupported configuration');
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
        $path = $this->determineAbsolutePath($name);
        $block = !is_null($path) ? $this->dm->find(null, $path) : null;

        if (empty($block)) {
            if ($this->logger) {
                $msg = !is_null($path)
                    ? "Block '$name' at path '$path' could not be found."
                    : "Block '$name' is not absolute path and there is no request attribute with 'contentDocument'."
                ;
                $this->logger->debug($msg);
            }

            return null;
        }

        return $block;
    }

    /**
     * @param string $path
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

    /**
     * @param string $name
     *
     * @return string|null path if determined, null if unable to determine
     */
    protected function determineAbsolutePath($name)
    {
        $path = null;

        if ($this->isAbsolutePath($name)) {
            $path = $name;
        } else if ($this->container->has('request')
            && $this->container->get('request')->attributes->has('contentDocument')
        ) {
            $currentPage = $this->container->get('request')->attributes->get('contentDocument');
            $path = $this->dm->getUnitOfWork()->getDocumentId($currentPage) . '/' . $name;
        }

        return $path;
    }

    /**
     * Get the block used when a block is not found or is invalid
     *
     * @param string $name The block name not found or invalid
     * @param string $message The exception message if an exception should be returned
     * @return \Symfony\Cmf\Bundle\BlockBundle\Model\EmptyBlock
     * @throws \Sonata\BlockBundle\Exception\BlockNotFoundException
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
