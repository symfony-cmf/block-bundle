<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\UnitOfWork;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\BlockBundle\Block\PhpcrBlockLoader;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PhpcrBlockLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $registryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dmMock;

    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pwcMock;
    private $requestStackMock;
    private $request;

    public function setUp()
    {
        $this->registryMock = $this->createMock(ManagerRegistry::class);
        $this->dmMock = $this->createMock(DocumentManager::class);
        $this->pwcMock = $this->createMock(AuthorizationCheckerInterface::class);
        $this->registryMock->expects($this->any())
            ->method('getManager')
            ->with($this->equalTo('themanager'))
            ->will($this->returnValue($this->dmMock))
        ;

        $this->request = Request::create('/');
        $this->requestStackMock = $this->createMock(RequestStack::class);
        $this->requestStackMock
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($this->request))
        ;
    }

    private function getSimpleBlockLoaderInstance()
    {
        $blockLoader = new PhpcrBlockLoader($this->registryMock, $this->pwcMock, $this->requestStackMock, null, 'emptyblocktype');
        $blockLoader->setManagerName('themanager');

        return $blockLoader;
    }

    public function testSupport()
    {
        $blockLoader = $this->getSimpleBlockLoaderInstance();

        $this->assertFalse($blockLoader->support('name'));
        $this->assertFalse($blockLoader->support([]));
        $this->assertTrue($blockLoader->support([
            'name' => 'someName',
        ]));
    }

    public function testLoadWithAbsolutePath()
    {
        $absoluteBlockPath = '/some/absolute/path';
        $block = $this->createMock(BlockInterface::class);

        $blockLoader = $this->getSimpleBlockLoaderInstance();
        $this->dmMock->expects($this->once())
            ->method('find')
            ->with(
                    $this->equalTo(null),
                    $this->equalTo($absoluteBlockPath)
            )
            ->will($this->returnValue($block))
        ;
        $this->pwcMock->expects($this->once())
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($block))
            ->will($this->returnValue(true))
        ;

        $found = $blockLoader->load(['name' => $absoluteBlockPath]);
        $this->assertEquals($block, $found);
    }

    public function testFindByNameWithRelativePath()
    {
        $contentPath = '/absolute/content';
        $relativeBlockPath = 'some/relative/path';
        $block = $this->createMock(BlockInterface::class);

        $content = new MockContent($contentPath);

        $parameterBagMock = $this->createMock(ParameterBag::class);
        $parameterBagMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('contentDocument'))
            ->will($this->returnValue($content))
        ;
        $parameterBagMock->expects($this->once())
            ->method('has')
            ->with($this->equalTo('contentDocument'))
            ->will($this->returnValue(true))
        ;

        $this->request->attributes = $parameterBagMock;

        $unitOfWorkMock = $this->createMock(UnitOfWork::class);
        $unitOfWorkMock->expects($this->any())
            ->method('getDocumentId')
            ->with($this->equalTo($content))
            ->will($this->returnValue($contentPath))
        ;

        $this->dmMock->expects($this->once())
            ->method('getUnitOfWork')
            ->will($this->returnValue($unitOfWorkMock))
        ;
        $this->dmMock->expects($this->once())
            ->method('find')
            ->with(
                    $this->equalTo(null),
                    $this->equalTo($contentPath.'/'.$relativeBlockPath)
            )
            ->will($this->returnValue($block))
        ;
        $this->pwcMock->expects($this->once())
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($block))
            ->will($this->returnValue(true))
        ;

        $blockLoader = $this->getSimpleBlockLoaderInstance();

        $found = $blockLoader->load(['name' => $relativeBlockPath]);
        $this->assertEquals($block, $found);
    }

    public function testLoadValidBlock()
    {
        $simpleBlock = new SimpleBlock();
        $absoluteBlockPath = '/some/absolute/path';

        $blockLoader = $this->getSimpleBlockLoaderInstance();
        $this->dmMock->expects($this->once())
            ->method('find')
            ->with(
                    $this->equalTo(null),
                    $this->equalTo($absoluteBlockPath)
            )
            ->will($this->returnValue($simpleBlock))
        ;
        $this->pwcMock->expects($this->once())
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($simpleBlock))
            ->will($this->returnValue(true))
        ;

        $receivedBlock = $blockLoader->load([
            'name' => $absoluteBlockPath,
        ]);

        $this->assertEquals($simpleBlock, $receivedBlock);
    }

    public function testLoadInvalidBlock()
    {
        $this->pwcMock->expects($this->never())
            ->method('isGranted')
        ;

        $blockLoader = $this->getSimpleBlockLoaderInstance();
        $this->assertInstanceOf('Sonata\BlockBundle\Model\EmptyBlock', $blockLoader->load(['name' => 'invalid/block']));
    }

    /**
     * Test using the block loader with two different document managers.
     */
    public function testLoadWithAlternativeDocumentManager()
    {
        $absoluteBlockPath = '/some/absolute/path';

        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('the-block'))
        ;

        $altBlock = $this->createMock(BlockInterface::class);
        $altBlock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('alt-block'))
        ;

        $this->dmMock->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo(null),
                $this->equalTo($absoluteBlockPath)
            )
            ->will($this->returnValue($block))
        ;

        $altDmMock = $this->createMock(DocumentManager::class);
        $altDmMock->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo(null),
                $this->equalTo($absoluteBlockPath)
            )
            ->will($this->returnValue($altBlock))
        ;
        $this->pwcMock->expects($this->at(0))
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($block))
            ->will($this->returnValue(true))
        ;
        $this->pwcMock->expects($this->at(1))
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($altBlock))
            ->will($this->returnValue(true))
        ;

        $registryMock = $this->createMock(ManagerRegistry::class);
        $registryMock->expects($this->at(0))
            ->method('getManager')
            ->with($this->equalTo('themanager'))
            ->will($this->returnValue($this->dmMock))
        ;
        $registryMock->expects($this->at(1))
            ->method('getManager')
            ->with($this->equalTo('altmanager'))
            ->will($this->returnValue($altDmMock))
        ;

        $blockLoader = new PhpcrBlockLoader($registryMock, $this->pwcMock, $this->requestStackMock, null, 'emptyblocktype');

        $blockLoader->setManagerName('themanager');
        $foundBlock = $blockLoader->load(['name' => $absoluteBlockPath]);
        $this->assertInstanceOf('Sonata\BlockBundle\Model\BlockInterface', $foundBlock);
        $this->assertEquals('the-block', $foundBlock->getName());

        $blockLoader->setManagerName('altmanager');
        $foundBlock = $blockLoader->load(['name' => $absoluteBlockPath]);
        $this->assertInstanceOf('Sonata\BlockBundle\Model\BlockInterface', $foundBlock);
        $this->assertEquals('alt-block', $foundBlock->getName());
    }
}

class MockContent
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
