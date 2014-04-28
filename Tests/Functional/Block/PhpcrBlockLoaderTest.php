<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Cmf\Bundle\BlockBundle\Block\PhpcrBlockLoader,
    Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @var SecurityContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $securityMock;

    public function setUp()
    {
        $this->registryMock = $this->getMockBuilder('Doctrine\Bundle\PHPCRBundle\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->dmMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->securityMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->registryMock->expects($this->any())
            ->method('getManager')
            ->with($this->equalTo('themanager'))
            ->will($this->returnValue($this->dmMock))
        ;
    }

    private function getSimpleBlockLoaderInstance()
    {
        $blockLoader = new PhpcrBlockLoader($this->registryMock, $this->securityMock, null, 'emptyblocktype');
        $blockLoader->setManagerName('themanager');

        return $blockLoader;
    }

    public function testSupport()
    {
        $blockLoader = $this->getSimpleBlockLoaderInstance();

        $this->assertFalse($blockLoader->support('name'));
        $this->assertFalse($blockLoader->support(array()));
        $this->assertTrue($blockLoader->support(array(
            'name' => 'someName'
        )));
    }

    public function testLoadWithAbsolutePath()
    {
        $absoluteBlockPath = '/some/absolute/path';
        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');

        $blockLoader = $this->getSimpleBlockLoaderInstance();
        $this->dmMock->expects($this->once())
            ->method('find')
            ->with(
                    $this->equalTo(null),
                    $this->equalTo($absoluteBlockPath)
            )
            ->will($this->returnValue($block))
        ;
        $this->securityMock->expects($this->once())
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($block))
            ->will($this->returnValue(true))
        ;

        $found = $blockLoader->load(array('name' => $absoluteBlockPath));
        $this->assertEquals($block, $found);
    }

    public function testFindByNameWithRelativePath()
    {
        $contentPath = '/absolute/content';
        $relativeBlockPath = 'some/relative/path';
        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');

        $content = new MockContent($contentPath);

        $parameterBagMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();
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

        $request = new Request();
        $request->attributes = $parameterBagMock;

        $unitOfWorkMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\UnitOfWork')
            ->disableOriginalConstructor()
            ->getMock();
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
                    $this->equalTo($contentPath . '/' . $relativeBlockPath)
            )
            ->will($this->returnValue($block))
        ;
        $this->securityMock->expects($this->once())
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($block))
            ->will($this->returnValue(true))
        ;

        $blockLoader = $this->getSimpleBlockLoaderInstance();
        $blockLoader->setRequest($request);

        $found = $blockLoader->load(array('name' => $relativeBlockPath));
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
        $this->securityMock->expects($this->once())
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($simpleBlock))
            ->will($this->returnValue(true))
        ;

        $receivedBlock = $blockLoader->load(array(
            'name' => $absoluteBlockPath
        ));

        $this->assertEquals($simpleBlock, $receivedBlock);
    }

    public function testLoadInvalidBlock()
    {
        $this->securityMock->expects($this->never())
            ->method('isGranted')
        ;

        $blockLoader = $this->getSimpleBlockLoaderInstance();
        $this->assertInstanceOf('Sonata\BlockBundle\Model\EmptyBlock', $blockLoader->load(array('name' => 'invalid/block')));
    }

    /**
     * Test using the block loader with two different document managers
     */
    public function testLoadWithAlternativeDocumentManager()
    {
        $absoluteBlockPath = '/some/absolute/path';

        $block = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
        $block->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('the-block'))
        ;

        $altBlock = $this->getMock('Sonata\BlockBundle\Model\BlockInterface');
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

        $altDmMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $altDmMock->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo(null),
                $this->equalTo($absoluteBlockPath)
            )
            ->will($this->returnValue($altBlock))
        ;
        $this->securityMock->expects($this->at(0))
            ->method('isGranted')
            ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($block))
            ->will($this->returnValue(true))
        ;
        $this->securityMock->expects($this->at(1))
        ->method('isGranted')
        ->with(PublishWorkflowChecker::VIEW_ATTRIBUTE, $this->equalTo($altBlock))
        ->will($this->returnValue(true))
    ;

        $registryMock = $this->getMockBuilder('Doctrine\Bundle\PHPCRBundle\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock()
        ;
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

        $blockLoader = new PhpcrBlockLoader($registryMock, $this->securityMock, null, 'emptyblocktype');

        $blockLoader->setManagerName('themanager');
        $foundBlock = $blockLoader->load(array('name' => $absoluteBlockPath));
        $this->assertInstanceOf('Sonata\BlockBundle\Model\BlockInterface', $foundBlock);
        $this->assertEquals('the-block', $foundBlock->getName());

        $blockLoader->setManagerName('altmanager');
        $foundBlock = $blockLoader->load(array('name' => $absoluteBlockPath));
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
