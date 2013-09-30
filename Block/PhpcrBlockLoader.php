<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
class PhpcrBlockLoader implements BlockLoaderInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var null|LoggerInterface
     */
    protected $logger;

    /**
     * @var string Name of object manager to use
     */
    protected $managerName;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The permission to check for when doing the publish workflow check.
     *
     * @var string
     */
    private $publishWorkflowPermission = PublishWorkflowChecker::VIEW_ATTRIBUTE;


    /**
     * @var string service id of the empty block service
     */
    protected $emptyBlockType;

    /**
     * @param ManagerRegistry          $managerRegistry
     * @param SecurityContextInterface $securityContext the publish workflow
     *      checker to check if menu items are published.
     * @param LoggerInterface          $logger
     * @param null                     $emptyBlockType  set this to a block type name if you want empty blocks returned when no block is found
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        SecurityContextInterface $securityContext,
        LoggerInterface $logger = null,
        $emptyBlockType = null
    ) {
        $this->managerRegistry  = $managerRegistry;
        $this->securityContext  = $securityContext;
        $this->logger           = $logger;
        $this->emptyBlockType   = $emptyBlockType;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Set the object manager name to use for this loader;
     * if not called, the default manager will be used.
     *
     * @param string $managerName
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
    }

    /**
     * What attribute to use in the publish workflow check. This typically
     * is VIEW or VIEW_ANONYMOUS.
     *
     * @param string $attribute
     */
    public function setPublishWorkflowPermission($attribute)
    {
        $this->publishWorkflowPermission = $attribute;
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
                null === $block ? 'not existing' : get_class($block)
            ));
        }

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

        $block = $this->getObjectManager()->find(null, $path);

        if (empty($block) && $this->logger) {
            $this->logger->debug("Block '$name' at path '$path' could not be found.");
        }

        if (!$this->securityContext->isGranted($this->publishWorkflowPermission, $block)) {
            if ($this->logger) {
                $this->logger->debug("Block '$name' at path '$path' is not published");
            }

            return null;
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

        if ($this->request
            && $this->request->attributes->has('contentDocument')
        ) {
            $currentPage = $this->request->attributes->get('contentDocument');

            return $this->getObjectManager()
                ->getUnitOfWork()
                ->getDocumentId($currentPage) . '/' . $name
            ;
        }

        return null;
    }

    /**
     * Get the block to return when a block is not found or the thing found was
     * no BlockInterface.
     *
     * If the empty block type is not set, throw an exception instead.
     *
     * @param string $name    The block name that was not found or invalid
     * @param string $message The exception message if an exception should be raised
     *
     * @return EmptyBlock
     *
     * @throws BlockNotFoundException if there is no type defined for the empty block.
     */
    private function getNotFoundBlock($name, $message = null)
    {
        if (null === $this->getEmptyBlockType()) {
            throw new BlockNotFoundException($message);
        }

        $block = new EmptyBlock();
        $block->setType($this->getEmptyBlockType());
        $block->setUpdatedAt(new \DateTime());

        $path = $this->determineAbsolutePath($name);
        if (null !== $path) {
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

    /**
     * Get the object manager from the registry, based on the current managerName
     *
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->managerRegistry->getManager($this->managerName);
    }
}
